<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bigtread extends Model
{

    protected $table = 'bigtire_treads';

    protected $primaryKey = 'tread_id';
    public $timestamps = false;

  use HasFactory;
}
