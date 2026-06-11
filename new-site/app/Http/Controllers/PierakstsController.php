<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Office;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Slot;
use App\Models\WorkingDay;
use App\Services\BookingService;
use App\Services\WorkingDaysProvisioner;
use App\Support\HalfSlotRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Публичная запись на услуги /pieraksts (бывш. Records\RecordController).
 * Календарь отдаётся как данные (view + JSON для фронта), бронь создаёт
 * BookingService, мягкая резервация — SlotReservationController.
 */
class PierakstsController extends Controller
{
    public function __construct(
        private WorkingDaysProvisioner $provisioner,
        private BookingService $bookings,
    ) {
    }

    public function index()
    {
        $days = $this->calendarData();

        return view('pieraksts.index', [
            'days' => $days,
            'offices' => Office::orderBy('id')->get(),
            'services' => Service::where('enabled', true)->orderBy('id')->get(),
        ]);
    }

    /** Сетка слотов на горизонт — JSON для фронта (и переиспользуется в index). */
    public function calendar(): JsonResponse
    {
        return response()->json(['days' => $this->calendarData()]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $result = $this->bookings->create(
            $request->validated(),
            $request->session()->getId(),
            $request->user()?->id
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function cancelForm(string $code)
    {
        $booking = Booking::query()->where('cancel_code', $code)->with('slot.queue.office')->first();

        if (! $booking || ! $booking->slot) {
            return redirect()->route('pieraksts')->with('warning', 'Pieraksts nav atrasts.');
        }
        if ($booking->slot->date->lt(now()->startOfDay())) {
            return redirect()->route('pieraksts')->with('warning', 'Jūsu pieraksts vairs nav aktuāls.');
        }

        $workingDay = WorkingDay::query()
            ->where('queue_id', $booking->slot->queue_id)
            ->where('date', $booking->slot->date->toDateString())
            ->where('is_draft', false)
            ->first();

        return view('pieraksts.cancel', [
            'booking' => $booking,
            'office' => $booking->slot->queue->office,
            'time' => $workingDay?->timeForPosition($booking->slot->position),
        ]);
    }

    public function cancelConfirm(Request $request, string $code)
    {
        $request->validate(['plate_suffix' => ['required', 'string', 'max:20']]);

        $result = $this->bookings->cancelByCode($code, $request->input('plate_suffix'));

        if ($result['success']) {
            return redirect()->route('pieraksts')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * @return array<string, mixed> дни → очереди → слоты со статусами
     */
    private function calendarData(): array
    {
        $dates = $this->provisioner->ensure();

        $workingDays = WorkingDay::query()
            ->whereIn('date', $dates)
            ->where('is_draft', false)
            ->where('is_visible', true)
            ->get()
            ->groupBy(fn (WorkingDay $w) => $w->date->toDateString());

        $queues = Queue::query()
            ->where('is_public', true)
            ->where('is_visible', true)
            ->orderBy('office_id')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id');

        $slots = Slot::query()
            ->whereIn('date', $dates)
            ->whereIn('queue_id', $queues->keys())
            ->with('booking:id,slot_id,car_brand,phone_number')
            ->get()
            ->keyBy(fn (Slot $s) => $s->queue_id.'|'.$s->date->toDateString().'|'.$s->position);

        $now = now();
        $days = [];

        foreach ($dates as $date) {
            $dayQueues = [];

            foreach ($workingDays->get($date, collect()) as $workingDay) {
                $queue = $queues->get($workingDay->queue_id);
                if (! $queue || ! $workingDay->is_opened) {
                    continue;
                }

                $grid = [];
                for ($position = 1; $position <= $workingDay->slotCount(); $position++) {
                    $slot = $slots->get($queue->id.'|'.$date.'|'.$position);
                    $grid[] = $this->describeSlot($slot, $position, $workingDay, $date, $now);
                }

                $dayQueues[] = [
                    'queue_id' => $queue->id,
                    'office_id' => $queue->office_id,
                    'title' => $queue->title,
                    'slots' => $grid,
                ];
            }

            $days[$date] = $dayQueues;
        }

        return $days;
    }

    /**
     * Состояние ячейки в терминах старой вёрстки (schedule.css):
     * kind: free | discount | moto | ac | taken | unavailable | reserved.
     * Правило старой системы: сегодня слот доступен только если до него
     * осталось не меньше 30 минут.
     *
     * @return array{position: int, time: string|null, kind: string, label: string}
     */
    private function describeSlot(?Slot $slot, int $position, WorkingDay $workingDay, string $date, \Carbon\Carbon $now): array
    {
        $time = $workingDay->timeForPosition($position);
        $role = HalfSlotRules::role($position, $workingDay);

        $bookable = ! ($date === $now->toDateString()
            && $time !== null
            && \Carbon\Carbon::parse($date.' '.$time)->subMinutes(30)->lt($now));

        $taken = $slot && ($slot->status !== Slot::STATUS_FREE || $slot->booking);

        if ($taken) {
            $booking = $slot->booking;
            $label = $booking
                ? mb_substr(trim((string) $booking->car_brand), 0, 6).' xxxxx'.substr((string) $booking->phone_number, -3)
                : 'Aizņemts';

            return ['position' => $position, 'time' => $time, 'kind' => 'taken', 'label' => $label];
        }

        if ($role === HalfSlotRules::ROLE_BLOCKED || ! $bookable) {
            return ['position' => $position, 'time' => $time, 'kind' => 'unavailable', 'label' => 'Aizņemts'];
        }

        if ($slot && $slot->isReserved()) {
            return ['position' => $position, 'time' => $time, 'kind' => 'reserved', 'label' => 'Rezervēts'];
        }

        if ($slot && $slot->comment !== null && $slot->comment !== '') {
            return ['position' => $position, 'time' => $time, 'kind' => 'discount', 'label' => $slot->comment];
        }

        if ($role === HalfSlotRules::ROLE_MOTO) {
            return ['position' => $position, 'time' => $time, 'kind' => 'moto', 'label' => 'Moto montāža'];
        }

        if ($role === HalfSlotRules::ROLE_AC) {
            return ['position' => $position, 'time' => $time, 'kind' => 'ac', 'label' => 'Kondicioniera apkope'];
        }

        return ['position' => $position, 'time' => $time, 'kind' => 'free', 'label' => 'Brīvs'];
    }
}
