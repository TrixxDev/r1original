<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Queue;
use App\Models\Slot;
use App\Models\WorkingDay;
use App\Support\HalfSlotRules;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Создание и отмена брони (бывш. RecordController::fillSlot / cancelSlot).
 *
 * Данные клиента живут в bookings (1:1 со slots), а не в JSON takenby.
 * Вся работа со слотом — в транзакции с lockForUpdate; UNIQUE-индексы
 * (date, queue_id, position) и bookings.slot_id страхуют от гонок.
 */
class BookingService
{
    public function __construct(private BookingNotifier $notifier)
    {
    }

    /**
     * @param array{
     *     queue_id: int, date: string, position: int, service_id: int,
     *     car_brand: string, car_model: string, license_plate: string,
     *     rims_with?: int|null, phone_number: string, email?: string|null,
     *     customer_comment?: string|null, is_mobile?: bool,
     *     car_info?: array|null, car_info_vnr?: string|null, car_info_source?: string|null,
     * } $data
     * @return array{success: bool, message: string, booking?: Booking}
     */
    public function create(array $data, string $sessionId, ?int $userId = null): array
    {
        $queue = Queue::find($data['queue_id']);
        if (! $queue || (! $userId && ! $queue->is_public)) {
            return ['success' => false, 'message' => 'Nederīga rinda.'];
        }

        $workingDay = WorkingDay::query()
            ->where('queue_id', $queue->id)
            ->where('date', $data['date'])
            ->where('is_draft', false)
            ->first();

        if (! $workingDay || ! $workingDay->is_opened) {
            return ['success' => false, 'message' => 'Šajā dienā pieraksts nav pieejams.'];
        }

        $halfError = HalfSlotRules::validateBooking((int) $data['position'], $workingDay, (int) $data['service_id']);
        if ($halfError !== null) {
            return ['success' => false, 'message' => $halfError];
        }

        $result = DB::transaction(function () use ($data, $sessionId, $userId) {
            $slot = Slot::query()
                ->where('queue_id', $data['queue_id'])
                ->where('date', $data['date'])
                ->where('position', $data['position'])
                ->lockForUpdate()
                ->first();

            if ($slot) {
                if ($slot->status !== Slot::STATUS_FREE || $slot->booking()->exists()) {
                    return ['success' => false, 'message' => 'Atvainojiet, jūsu izvēlētais laiks vairs nav pieejams!'];
                }
                if ($slot->isReserved() && $slot->reserved_by !== $sessionId) {
                    return ['success' => false, 'message' => 'Laiks ir rezervēts citam lietotājam. Lūdzu, izvēlieties citu laiku.'];
                }
            } else {
                $slot = new Slot([
                    'queue_id' => $data['queue_id'],
                    'date' => $data['date'],
                    'position' => $data['position'],
                ]);
            }

            $slot->status = Slot::STATUS_BOOKED;
            $slot->reserved_until = null;
            $slot->reserved_by = null;
            $slot->extension_count = 0;
            $slot->version++;
            $slot->created_by = $userId;
            $slot->save();

            $booking = Booking::create([
                'slot_id' => $slot->id,
                'service_id' => $data['service_id'],
                'cancel_code' => $this->generateCancelCode(),
                'car_brand' => $data['car_brand'],
                'car_model' => $data['car_model'],
                'license_plate' => mb_strtoupper(trim($data['license_plate'])),
                'rims_with' => $data['rims_with'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'phone_number' => $data['phone_number'],
                'email' => $data['email'] ?? null,
                'customer_comment' => $data['customer_comment'] ?? null,
                'is_mobile' => (bool) ($data['is_mobile'] ?? false),
                'car_info' => $data['car_info'] ?? null,
                'car_info_vnr' => $data['car_info_vnr'] ?? null,
                'car_info_fetched_at' => isset($data['car_info']) ? Carbon::now() : null,
                'car_info_source' => $data['car_info_source'] ?? null,
                'created_by' => $userId,
            ]);

            return ['success' => true, 'booking' => $booking->setRelation('slot', $slot)];
        });

        if (! $result['success']) {
            return $result;
        }

        $booking = $result['booking'];
        $time = $workingDay->timeForPosition((int) $data['position']);

        $this->notifier->bookingCreated($booking, $queue, $time);

        return [
            'success' => true,
            'message' => sprintf(
                'Paldies par pierakstu! Gaidīsim jūs %s plkst. %s — %s.',
                Carbon::parse($data['date'])->format('d.m.Y'),
                $time,
                $queue->office->title
            ),
            'booking' => $booking,
        ];
    }

    /**
     * Отмена по cancel_code; подтверждение — последние 2 символа номерного знака.
     *
     * @return array{success: bool, message: string}
     */
    public function cancelByCode(string $cancelCode, string $plateSuffix): array
    {
        $booking = Booking::query()->where('cancel_code', $cancelCode)->with('slot.queue.office')->first();

        if (! $booking || ! $booking->slot) {
            return ['success' => false, 'message' => 'Pieraksts nav atrasts.'];
        }

        if ($booking->slot->date->lt(Carbon::today())) {
            return ['success' => false, 'message' => 'Jūsu pieraksts vairs nav aktuāls.'];
        }

        $expected = mb_substr((string) $booking->license_plate, -2);
        if (mb_strtoupper(trim($plateSuffix)) !== mb_strtoupper($expected)) {
            return ['success' => false, 'message' => 'Numurs ievadīts nepareizi. Mēģiniet vēlreiz vai sazinieties ar mums telefoniski.'];
        }

        return $this->performCancel($booking);
    }

    /** Отмена брони администратором — без проверки номера и даты, клиент уведомляется. */
    public function adminCancel(Booking $booking): array
    {
        $booking->loadMissing('slot.queue.office');

        if (! $booking->slot) {
            return ['success' => false, 'message' => 'Pieraksts nav atrasts.'];
        }

        return $this->performCancel($booking);
    }

    /** @return array{success: bool, message: string} */
    private function performCancel(Booking $booking): array
    {
        $queue = $booking->slot->queue;
        $workingDay = WorkingDay::query()
            ->where('queue_id', $booking->slot->queue_id)
            ->where('date', $booking->slot->date->toDateString())
            ->where('is_draft', false)
            ->first();
        $time = $workingDay?->timeForPosition($booking->slot->position) ?? '';

        // Снимок до удаления — для уведомлений.
        $snapshot = $booking->replicate();
        $snapshot->setRelation('slot', $booking->slot->replicate());

        DB::transaction(function () use ($booking) {
            $slot = Slot::query()->whereKey($booking->slot_id)->lockForUpdate()->first();
            $booking->delete();

            if ($slot) {
                if (empty($slot->comment)) {
                    $slot->delete();
                } else {
                    $slot->status = Slot::STATUS_FREE;
                    $slot->version++;
                    $slot->save();
                }
            }
        });

        $this->notifier->bookingCancelled($snapshot, $queue, $time);

        return ['success' => true, 'message' => 'Atcelšana ir izdevusies.'];
    }

    private function generateCancelCode(): string
    {
        do {
            $code = Str::lower(Str::random(24));
        } while (Booking::query()->where('cancel_code', $code)->exists());

        return $code;
    }
}
