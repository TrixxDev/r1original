<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'pdf_title', 'allows_storage', 'allows_car', 'allows_moto', 'enabled',
    ];

    protected $casts = [
        'allows_storage' => 'bool',
        'allows_car' => 'bool',
        'allows_moto' => 'bool',
        'enabled' => 'bool',
    ];
}
