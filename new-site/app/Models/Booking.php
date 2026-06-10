<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'slot_id', 'service_id', 'cancel_code', 'car_brand', 'car_model',
        'license_plate', 'rims_with', 'phone_number', 'email', 'customer_comment',
        'discount', 'is_mobile', 'work_status', 'ic_status', 'planned_tasks',
        'lift_spot', 'car_info', 'car_info_vnr', 'car_info_fetched_at',
        'car_info_source', 'legacy_data', 'created_by',
    ];

    protected $casts = [
        'car_info' => 'array',
        'legacy_data' => 'array',
        'is_mobile' => 'bool',
        'car_info_fetched_at' => 'datetime',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
