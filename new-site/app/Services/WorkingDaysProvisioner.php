<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\WorkingDay;
use Carbon\Carbon;

/**
 * Автосоздание рабочих дней на горизонт вперёд по умолчаниям очереди.
 *
 * Перенос из старого WorkingDaysProvisioner, но вместо двух таблиц
 * (workingdays + new_workingdays) — одна working_days с флагом is_draft:
 * is_draft=false — опубликованная строка, is_draft=true — черновик админки.
 */
class WorkingDaysProvisioner
{
    public const DEFAULT_HORIZON_DAYS = 8;

    /** @return list<string> даты горизонта Y-m-d */
    public function ensure(int $horizonDays = self::DEFAULT_HORIZON_DAYS): array
    {
        $dates = [];
        for ($i = 0; $i <= $horizonDays; $i++) {
            $dates[] = Carbon::today()->addDays($i)->toDateString();
        }

        $queues = Queue::query()
            ->orderBy('office_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($queues->isEmpty()) {
            return $dates;
        }

        $existing = WorkingDay::query()
            ->whereIn('date', $dates)
            ->get(['queue_id', 'date', 'is_draft'])
            ->keyBy(fn (WorkingDay $w) => $w->queue_id.'|'.$w->date->toDateString().'|'.(int) $w->is_draft);

        foreach ($dates as $date) {
            $day = Carbon::parse($date);
            $weekday = (int) $day->format('N');
            $isWeekend = $day->isWeekend();

            foreach ($queues as $queue) {
                $timeOpen = $isWeekend ? $queue->weekend_time_open : $queue->time_open;
                $timeClose = $isWeekend ? $queue->weekend_time_close : $queue->time_close;
                // Воскресенье закрыто всегда (как в старой системе)
                $opened = $queue->is_visible && $weekday !== 7;

                foreach ([false, true] as $isDraft) {
                    if ($existing->has($queue->id.'|'.$date.'|'.(int) $isDraft)) {
                        continue;
                    }

                    WorkingDay::create([
                        'queue_id' => $queue->id,
                        'date' => $date,
                        'is_draft' => $isDraft,
                        'time_open' => $timeOpen,
                        'time_close' => $timeClose,
                        'is_opened' => $opened,
                        'is_visible' => $opened,
                    ]);
                }
            }
        }

        return $dates;
    }
}
