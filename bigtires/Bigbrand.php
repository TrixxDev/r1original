<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bigbrand extends Model
{

    protected $table = 'bigtire_brands';

    protected $primaryKey = 'brand_id';
    public $timestamps = false;

    use HasFactory;
}
