<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Autotire;
use App\Models\ShopMobileCartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $guestId = $request->attributes->get('shop_guest_id');

        return response()->json($this->buildCartPayload($guestId));
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tire_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $guestId = $request->attributes->get('shop_guest_id');

        $tire = Autotire::where('tire_id', $data['tire_id'])->first();
        if (! $tire) {
            return response()->json(['message' => 'Riepa nav atrasta'], 404);
        }

        DB::transaction(function () use ($guestId, $data) {
            $existing = ShopMobileCartItem::where('guest_id', $guestId)
                ->where('tire_id', $data['tire_id'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->quantity += $data['quantity'];
                $existing->price = $data['price'];
                $existing->save();
            } else {
                ShopMobileCartItem::create([
                    'guest_id' => $guestId,
                    'tire_id' => $data['tire_id'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                ]);
            }
        });

        return response()->json($this->buildCartPayload($guestId));
    }

    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $guestId = $request->attributes->get('shop_guest_id');

        $item = ShopMobileCartItem::where('guest_id', $guestId)->where('id', $itemId)->first();
        if (! $item) {
            return response()->json(['message' => 'Pozīcija nav atrasta'], 404);
        }

        $item->quantity = $data['quantity'];
        $item->save();

        return response()->json($this->buildCartPayload($guestId));
    }

    public function deleteItem(Request $request, int $itemId): JsonResponse
    {
        $guestId = $request->attributes->get('shop_guest_id');

        $item = ShopMobileCartItem::where('guest_id', $guestId)->where('id', $itemId)->first();
        if (! $item) {
            return response()->json(['message' => 'Pozīcija nav atrasta'], 404);
        }

        $item->delete();

        return response()->json($this->buildCartPayload($guestId));
    }

    private function buildCartPayload(string $guestId): array
    {
        $items = ShopMobileCartItem::where('guest_id', $guestId)->get();
        $lines = [];
        $total = 0.0;

        foreach ($items as $row) {
            $line = (float) $row->price * (int) $row->quantity;
            $total += $line;
            $lines[] = [
                'id' => $row->id,
                'tire_id' => $row->tire_id,
                'quantity' => $row->quantity,
                'price' => (float) $row->price,
                'line_total' => $line,
            ];
        }

        return [
            'guest_id' => $guestId,
            'items' => $lines,
            'total' => round($total, 2),
        ];
    }
}

