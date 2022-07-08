<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quadrtread extends Model
{
    use HasFactory;

    protected $table = 'quadr_treads';

    protected $primaryKey = 'tread_id';
}
