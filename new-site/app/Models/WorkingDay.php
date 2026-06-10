<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingDay extends Model
{
    protected $fillable = [
        'queue_id', 'date', 'is_draft', 'time_open', 'time_close', 'time_step',
        'is_opened', 'is_visible', 'is_half', 'ac_toggle', 'moto_toggle',
    ];

    protected $casts = [
        'date' => 'date',
        'is_draft' => 'bool',
        'is_opened' => 'bool',
        'is_visible' => 'bool',
        'is_half' => 'bool',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    /**
     * Храним дату строго как Y-m-d: иначе cast `date` пишет «Y-m-d 00:00:00»
     * и строковое сравнение where('date', ...) ломается на SQLite.
     */
    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    /** Время слота по позиции (1-based): time_open + (position-1) * time_step. */
    public function timeForPosition(int $position): ?string
    {
        if (! $this->time_open) {
            return null;
        }

        return \Carbon\Carbon::parse($this->time_open)
            ->addMinutes(($position - 1) * ($this->time_step ?: 15))
            ->format('H:i');
    }

    /** Количество слотов в сетке дня. */
    public function slotCount(): int
    {
        if (! $this->time_open || ! $this->time_close) {
            return 0;
        }

        $open = \Carbon\Carbon::parse($this->time_open);
        $close = \Carbon\Carbon::parse($this->time_close);
        $step = $this->time_step ?: 15;

        return max(0, (int) floor($open->diffInMinutes($close) / $step));
    }
}
