<?php

namespace App\Services;

use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Мягкая резервация слота на время заполнения формы.
 *
 * Перенос логики из старого проекта (5 минут, максимум 2 продления),
 * но теперь на InnoDB транзакции и lockForUpdate() работают по-настоящему,
 * а UNIQUE (date, queue_id, position) исключает дубли слота.
 */
class SlotReservationService
{
    public const RESERVATION_TIMEOUT_MINUTES = 5;
    public const MAX_EXTENSIONS = 2;

    /**
     * @return array{success: bool, message: string, slot?: Slot, reserved_until?: string}
     */
    public function reserve(int $queueId, string $date, int $position, string $sessionId): array
    {
        return DB::transaction(function () use ($queueId, $date, $position, $sessionId) {
            $this->releaseExpired();

            $slot = Slot::query()
                ->where('queue_id', $queueId)
                ->where('date', $date)
                ->where('position', $position)
                ->lockForUpdate()
                ->first();

            if (! $slot) {
                $slot = Slot::create([
                    'queue_id' => $queueId,
                    'date' => $date,
                    'position' => $position,
                ]);
            }

            if ($slot->status !== Slot::STATUS_FREE || $slot->booking()->exists()) {
                return ['success' => false, 'message' => 'Laiks jau ir aizņemts'];
            }

            if ($slot->isReserved() && $slot->reserved_by !== $sessionId) {
                return [
                    'success' => false,
                    'message' => 'Laiks ir rezervēts citam lietotājam',
                    'reserved_until' => $slot->reserved_until->toIso8601String(),
                ];
            }

            // Повторный запрос той же сессии продлевает срок,
            // но не сбрасывает счётчик продлений.
            if (! ($slot->isReserved() && $slot->reserved_by === $sessionId)) {
                $slot->extension_count = 0;
            }

            $slot->reserved_until = Carbon::now()->addMinutes(self::RESERVATION_TIMEOUT_MINUTES);
            $slot->reserved_by = $sessionId;
            $slot->version++;
            $slot->save();

            return [
                'success' => true,
                'message' => 'Laiks rezervēts uz ' . self::RESERVATION_TIMEOUT_MINUTES . ' minūtēm',
                'slot' => $slot,
                'reserved_until' => $slot->reserved_until->toIso8601String(),
            ];
        });
    }

    /**
     * @return array{success: bool, message: string, extensions_left?: int, reserved_until?: string}
     */
    public function extend(int $slotId, string $sessionId): array
    {
        return DB::transaction(function () use ($slotId, $sessionId) {
            $slot = Slot::query()->whereKey($slotId)->lockForUpdate()->first();

            if (! $slot || ! $slot->isReserved() || $slot->reserved_by !== $sessionId) {
                return ['success' => false, 'message' => 'Rezervācija nav atrasta vai beigusies'];
            }

            if ($slot->extension_count >= self::MAX_EXTENSIONS) {
                $this->clearReservation($slot);

                return ['success' => false, 'message' => 'Pagarināšanas iespējas ir izmantotas'];
            }

            $slot->reserved_until = Carbon::now()->addMinutes(self::RESERVATION_TIMEOUT_MINUTES);
            $slot->extension_count++;
            $slot->version++;
            $slot->save();

            return [
                'success' => true,
                'message' => 'Rezervācija pagarināta',
                'extensions_left' => self::MAX_EXTENSIONS - $slot->extension_count,
                'reserved_until' => $slot->reserved_until->toIso8601String(),
            ];
        });
    }

    public function cancel(int $slotId, string $sessionId): bool
    {
        return DB::transaction(function () use ($slotId, $sessionId) {
            $slot = Slot::query()->whereKey($slotId)->lockForUpdate()->first();

            if (! $slot || $slot->reserved_by !== $sessionId) {
                return false;
            }

            $this->clearReservation($slot);

            return true;
        });
    }

    /** Снять все истёкшие резервации, удалить пустые слоты (вызывается и кроном). */
    public function releaseExpired(): int
    {
        $expired = Slot::query()
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<', Carbon::now())
            ->get();

        foreach ($expired as $slot) {
            $this->clearReservation($slot);
        }

        return $expired->count();
    }

    private function clearReservation(Slot $slot): void
    {
        $isEmpty = $slot->status === Slot::STATUS_FREE
            && empty($slot->comment)
            && ! $slot->booking()->exists();

        if ($isEmpty) {
            $slot->delete();

            return;
        }

        $slot->reserved_until = null;
        $slot->reserved_by = null;
        $slot->extension_count = 0;
        $slot->version++;
        $slot->save();
    }
}
