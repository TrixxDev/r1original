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
}
