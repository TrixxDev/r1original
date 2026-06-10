<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'session_id', 'status',
        'customer_name', 'customer_surname', 'email', 'phone_country_code', 'phone_number',
        'company_reg_nr', 'company_pvn_nr', 'company_name', 'company_address',
        'comments', 'car_details', 'email_notification',
        'promo_code', 'discount_type', 'discount_value',
        'total_price', 'delivery_price', 'mounting_price',
        'delivery_method', 'delivery_city', 'delivery_address', 'door_code',
        'mounting_office_id', 'admin_info', 'edited_by',
        'legacy_details', 'expires_at', 'legacy_id',
    ];

    protected $casts = [
        'legacy_details' => 'array',
        'email_notification' => 'bool',
        'expires_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
