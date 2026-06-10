<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Services\SlotReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Мягкая резервация слота на время заполнения формы
 * (бывш. Records\SlotLockingController). Все ответы — JSON для фронта.
 */
class SlotReservationController extends Controller
{
    public function __construct(private SlotReservationService $service)
    {
    }

    public function reserve(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'position' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->service->reserve(
            (int) $data['queue_id'],
            $data['date'],
            (int) $data['position'],
            $request->session()->getId()
        );

        if (isset($result['slot'])) {
            $result['slot_id'] = $result['slot']->id;
            unset($result['slot']);
        }

        return response()->json($result, $result['success'] ? 200 : 409);
    }

    public function extend(Request $request): JsonResponse
    {
        $data = $request->validate(['slot_id' => ['required', 'integer']]);

        $result = $this->service->extend((int) $data['slot_id'], $request->session()->getId());

        return response()->json($result, $result['success'] ? 200 : 409);
    }

    public function cancel(Request $request): JsonResponse
    {
        $data = $request->validate(['slot_id' => ['required', 'integer']]);

        $ok = $this->service->cancel((int) $data['slot_id'], $request->session()->getId());

        return response()->json(['success' => $ok]);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => ['required', 'integer'],
            'date' => ['required', 'date_format:Y-m-d'],
            'position' => ['required', 'integer', 'min:1'],
        ]);

        $slot = Slot::query()
            ->where('queue_id', $data['queue_id'])
            ->where('date', $data['date'])
            ->where('position', $data['position'])
            ->first();

        $sessionId = $request->session()->getId();
        $available = ! $slot
            || ($slot->status === Slot::STATUS_FREE
                && ! $slot->booking()->exists()
                && (! $slot->isReserved() || $slot->reserved_by === $sessionId));

        return response()->json(['available' => $available]);
    }
}
