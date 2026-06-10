<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    public const STATUS_FREE = 0;
    public const STATUS_BOOKED = 1;
    public const STATUS_BLOCKED = 2;

    protected $fillable = [
        'queue_id', 'date', 'position', 'status', 'comment',
        'reserved_until', 'reserved_by', 'extension_count',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'reserved_until' => 'datetime',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    public function isReserved(): bool
    {
        return $this->reserved_until !== null && $this->reserved_until->isFuture();
    }
}
