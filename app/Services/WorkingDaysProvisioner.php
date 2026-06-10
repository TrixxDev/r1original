<?php

namespace App\Services;

use App\Models\NewWorkingDay;
use App\Models\Queue;
use App\Models\Workingday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class WorkingDaysProvisioner
{
    public const DEFAULT_HORIZON_DAYS = 14;

    /**
     * Ensure workingdays and new_workingdays rows exist for every queue × date in the horizon.
     *
     * @return string[] Dates covered (today .. today + horizonDays)
     */
    public function ensure(int $horizonDays = self::DEFAULT_HORIZON_DAYS): array
    {
        $daysToShow = $this->buildDateRange($horizonDays);

        $queues = Queue::query()
            ->orderBy('office_id')
            ->orderBy('iorder')
            ->orderBy('queue_id')
            ->get();

        if ($queues->isEmpty()) {
            return $daysToShow;
        }

        $publishedKeys = $this->existingPairKeys(Workingday::class, $daysToShow);
        $draftKeys = $this->existingPairKeys(NewWorkingDay::class, $daysToShow);
        $workingdaysHasIsVisible = Schema::hasColumn('workingdays', 'is_visible');

        foreach ($daysToShow as $date) {
            $weekday = (int) Carbon::parse($date)->format('N');
            $isWeekend = Carbon::parse($date)->isWeekend();

            foreach ($queues as $queue) {
                $key = $date . '|' . $queue->queue_id;
                $needsPublished = ! isset($publishedKeys[$key]);
                $needsDraft = ! isset($draftKeys[$key]);

                if (! $needsPublished && ! $needsDraft) {
                    continue;
                }

                $timeOpen = $isWeekend ? $queue->wtimeopen : $queue->timeopen;
                $timeClose = $isWeekend ? $queue->wtimeclose : $queue->timeclose;
                $opened = ($queue->is_visible == 1) ? 1 : 0;
                if ($weekday === 7) {
                    $opened = 0;
                }

                if ($needsPublished) {
                    $workingDay = new Workingday();
                    $workingDay->timestamps = false;
                    $workingDay->queue_id = $queue->queue_id;
                    $workingDay->office_id = $queue->office_id;
                    $workingDay->date = $date;
                    $workingDay->weekday = $weekday;
                    $workingDay->timeopen = $timeOpen;
                    $workingDay->timeclose = $timeClose;
                    $workingDay->is_opened = $opened;
                    if ($workingdaysHasIsVisible) {
                        $workingDay->is_visible = $opened;
                    }
                    $workingDay->save();
                    $publishedKeys[$key] = true;
                } elseif ($needsDraft) {
                    $workingDay = Workingday::where('date', $date)
                        ->where('queue_id', $queue->queue_id)
                        ->first();
                }

                if ($needsDraft) {
                    $this->saveDraftRow($workingDay, $queue, $date, $weekday, $timeOpen, $timeClose, $opened);
                    $draftKeys[$key] = true;
                }
            }
        }

        return $daysToShow;
    }

    /**
     * @return string[]
     */
    private function buildDateRange(int $horizonDays): array
    {
        $days = [];
        for ($i = 0; $i <= $horizonDays; $i++) {
            $days[] = date('Y-m-d', strtotime('+' . $i . ' days'));
        }

        return $days;
    }

    /**
     * @param  class-string<Workingday|NewWorkingDay>  $modelClass
     * @return array<string, true>
     */
    private function existingPairKeys(string $modelClass, array $dates): array
    {
        $keys = [];
        $modelClass::whereIn('date', $dates)
            ->get(['date', 'queue_id'])
            ->each(function ($row) use (&$keys) {
                $keys[$row->date . '|' . $row->queue_id] = true;
            });

        return $keys;
    }

    private function saveDraftRow(
        ?Workingday $source,
        Queue $queue,
        string $date,
        int $weekday,
        $timeOpen,
        $timeClose,
        int $opened
    ): void {
        if ($source !== null) {
            $draft = $source->replicate();
            $draft->setTable('new_workingdays');
            $draft->timestamps = false;
            unset($draft->is_visible);
            $draft->save();

            return;
        }

        $draft = new NewWorkingDay();
        $draft->timestamps = false;
        $draft->queue_id = $queue->queue_id;
        $draft->office_id = $queue->office_id;
        $draft->date = $date;
        $draft->weekday = $weekday;
        $draft->timeopen = $timeOpen;
        $draft->timeclose = $timeClose;
        $draft->is_opened = $opened;
        $draft->save();
    }
}
