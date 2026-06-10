<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopMobileCartItem extends Model
{
    protected $table = 'shop_mobile_cart_items';

    protected $fillable = [
        'guest_id',
        'tire_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'float',
    ];
}

