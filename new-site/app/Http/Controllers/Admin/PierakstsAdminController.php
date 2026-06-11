<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Office;
use App\Models\Slot;
use App\Models\WorkingDay;
use App\Services\BookingService;
use App\Services\WorkingDaysProvisioner;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Админка записи (бывш. Admin\Records\RecordController):
 * сетка дня по всем очередям обоих филиалов (включая непубличные),
 * детали брони, отмена брони, блокировка слота, скидка-комментарий.
 */
class PierakstsAdminController extends Controller
{
    public function __construct(
        private WorkingDaysProvisioner $provisioner,
        private BookingService $bookings,
    ) {
    }

    public function index(Request $request, ?string $date = null)
    {
        $date = $date ?: $request->query('date', Carbon::today()->toDateString());
        try {
            $day = Carbon::parse($date);
        } catch (\Exception) {
            $day = Carbon::today();
        }
        $date = $day->toDateString();

        $this->provisioner->ensure();

        $offices = Office::with(['queues' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->orderBy('id')
            ->get();

        $workingDays = WorkingDay::query()
            ->where('date', $date)
            ->where('is_draft', false)
            ->get()
            ->keyBy('queue_id');

        $slots = Slot::query()
            ->where('date', $date)
            ->with('booking.service')
            ->get()
            ->keyBy(fn (Slot $s) => $s->queue_id.'|'.$s->position);

        // Общая сетка строк: от самого раннего открытия до самого позднего закрытия
        $open = null;
        $close = null;
        $step = 15;
        foreach ($workingDays as $wd) {
            if (! $wd->time_open || ! $wd->time_close) {
                continue;
            }
            $open = $open === null ? $wd->time_open : min($open, $wd->time_open);
            $close = $close === null ? $wd->time_close : max($close, $wd->time_close);
            $step = $wd->time_step ?: 15;
        }

        $times = [];
        if ($open !== null && $close !== null) {
            $cursor = Carbon::parse($open);
            $end = Carbon::parse($close);
            while ($cursor->lt($end)) {
                $times[] = $cursor->format('H:i');
                $cursor->addMinutes($step);
            }
        }

        $grid = [];
        foreach ($offices as $office) {
            $officeQueues = [];
            foreach ($office->queues as $queue) {
                $wd = $workingDays->get($queue->id);
                $cells = [];
                foreach ($times as $time) {
                    $cells[$time] = $this->describeCell($queue->id, $wd, $time, $slots);
                }
                $officeQueues[] = ['queue' => $queue, 'workingDay' => $wd, 'cells' => $cells];
            }
            $grid[] = ['office' => $office, 'queues' => $officeQueues];
        }

        return view('admin.pieraksts.index', [
            'date' => $date,
            'day' => $day,
            'times' => $times,
            'grid' => $grid,
        ]);
    }

    /** Заблокировать свободный слот (status=2, «Slēgts»). */
    public function block(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'position' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data) {
            $slot = Slot::query()
                ->where('queue_id', $data['queue_id'])
                ->where('date', $data['date'])
                ->where('position', $data['position'])
                ->lockForUpdate()
                ->first();

            if ($slot && ($slot->booking()->exists() || $slot->status === Slot::STATUS_BOOKED)) {
                return response()->json(['success' => false, 'message' => 'Slots jau ir aizņemts.'], 409);
            }

            $slot ??= new Slot($data + ['status' => Slot::STATUS_FREE]);
            $slot->status = Slot::STATUS_BLOCKED;
            $slot->version++;
            $slot->save();

            return response()->json(['success' => true]);
        });
    }

    /** Снять блокировку; пустой слот удаляется. */
    public function unblock(Request $request): JsonResponse
    {
        $data = $request->validate(['slot_id' => ['required', 'integer']]);

        return DB::transaction(function () use ($data) {
            $slot = Slot::query()->whereKey($data['slot_id'])->lockForUpdate()->first();

            if (! $slot || $slot->status !== Slot::STATUS_BLOCKED) {
                return response()->json(['success' => false, 'message' => 'Slots nav bloķēts.'], 409);
            }

            if (empty($slot->comment)) {
                $slot->delete();
            } else {
                $slot->status = Slot::STATUS_FREE;
                $slot->version++;
                $slot->save();
            }

            return response()->json(['success' => true]);
        });
    }

    /** Скидка/комментарий на свободном слоте (бывш. discount). Пустая строка убирает. */
    public function comment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'position' => ['required', 'integer', 'min:1'],
            'comment' => ['nullable', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($data) {
            $slot = Slot::query()
                ->where('queue_id', $data['queue_id'])
                ->where('date', $data['date'])
                ->where('position', $data['position'])
                ->lockForUpdate()
                ->first();

            $comment = trim((string) ($data['comment'] ?? ''));

            if (! $slot) {
                if ($comment === '') {
                    return response()->json(['success' => true]);
                }
                $slot = new Slot([
                    'queue_id' => $data['queue_id'],
                    'date' => $data['date'],
                    'position' => $data['position'],
                    'status' => Slot::STATUS_FREE,
                ]);
            }

            $slot->comment = $comment === '' ? null : $comment;
            $slot->version++;

            // Пустой свободный слот без комментария не храним
            if ($slot->exists && $slot->comment === null
                && $slot->status === Slot::STATUS_FREE
                && ! $slot->booking()->exists()
                && ! $slot->isReserved()) {
                $slot->delete();
            } else {
                $slot->save();
            }

            return response()->json(['success' => true]);
        });
    }

    /** Отмена брони админом (клиент получает уведомление об отмене). */
    public function cancelBooking(Request $request): JsonResponse
    {
        $data = $request->validate(['booking_id' => ['required', 'integer']]);

        $booking = Booking::find($data['booking_id']);
        if (! $booking) {
            return response()->json(['success' => false, 'message' => 'Pieraksts nav atrasts.'], 404);
        }

        $result = $this->bookings->adminCancel($booking);

        return response()->json($result, $result['success'] ? 200 : 409);
    }

    /**
     * @param \Illuminate\Support\Collection<string, Slot> $slots
     * @return array<string, mixed>
     */
    private function describeCell(int $queueId, ?WorkingDay $wd, string $time, $slots): array
    {
        if (! $wd || ! $wd->is_opened || ! $wd->time_open || ! $wd->time_close) {
            return ['state' => 'closed'];
        }

        $step = $wd->time_step ?: 15;
        $offset = Carbon::parse($wd->time_open)->diffInMinutes(Carbon::parse($time), false);
        $lastStart = Carbon::parse($wd->time_open)->diffInMinutes(Carbon::parse($wd->time_close)) - $step;

        if ($offset < 0 || $offset > $lastStart || $offset % $step !== 0) {
            return ['state' => 'closed'];
        }

        $position = intdiv($offset, $step) + 1;
        $slot = $slots->get($queueId.'|'.$position);

        $cell = ['state' => 'free', 'position' => $position];

        if (! $slot) {
            return $cell;
        }

        $cell['slot_id'] = $slot->id;
        $cell['comment'] = $slot->comment;

        if ($slot->booking) {
            $b = $slot->booking;
            $cell['state'] = 'booked';
            $cell['booking'] = [
                'id' => $b->id,
                'car' => trim($b->car_brand.' '.$b->car_model),
                'plate' => $b->license_plate,
                'name' => $b->customer_name,
                'phone' => $b->phone_number,
                'email' => $b->email,
                'service' => $b->service?->title,
                'rims_with' => $b->rims_with,
                'comment' => $b->customer_comment,
                'created_at' => $b->created_at?->format('d.m.Y H:i'),
            ];
        } elseif ($slot->status === Slot::STATUS_BLOCKED) {
            $cell['state'] = 'blocked';
        } elseif ($slot->isReserved()) {
            $cell['state'] = 'reserved';
        } elseif ($slot->comment) {
            $cell['state'] = 'discount';
        }

        return $cell;
    }
}
