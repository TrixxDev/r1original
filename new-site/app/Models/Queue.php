<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Queue extends Model
{
    protected $fillable = [
        'office_id', 'title', 'is_visible', 'is_public', 'sort_order',
        'time_open', 'time_close', 'weekend_time_open', 'weekend_time_close',
        'notification_subject', 'notification_email', 'notification_cancel_email',
        'notification_sms', 'notification_schedule_sms', 'notification_schedule_cancel_sms',
    ];

    protected $casts = [
        'is_visible' => 'bool',
        'is_public' => 'bool',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function workingDays(): HasMany
    {
        return $this->hasMany(WorkingDay::class);
    }
}
