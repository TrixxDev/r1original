<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopMobileCartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Paysera: struktūra klientam; izvēles MOCK URL no .env.
 */
class CheckoutController extends Controller
{
    public function prepare(Request $request): JsonResponse
    {
        $request->validate([
            'return_url' => 'nullable|string|max:2048',
        ]);

        $guestId = $request->attributes->get('shop_guest_id');

        $count = ShopMobileCartItem::where('guest_id', $guestId)->count();
        if ($count === 0) {
            return response()->json([
                'message' => 'Grozs ir tukšs',
                'order_id' => null,
                'payment_url' => null,
            ], 422);
        }

        $paymentUrl = config('shop.mobile_mock_payment_url');

        return response()->json([
            'order_id' => null,
            'payment_url' => $paymentUrl ?: null,
            'message' => $paymentUrl
                ? 'Testa maksājuma URL (shop.mobile_mock_payment_url vai Paysera integrācija serverī).'
                : 'Gaida Paysera integrāciju: jāievieš pasūtījuma izveide un novirzīšanas URL serverī.',
            'return_url' => $request->input('return_url'),
        ]);
    }
}

