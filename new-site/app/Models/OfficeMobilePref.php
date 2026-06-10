<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeMobilePref extends Model
{
    protected $primaryKey = 'office_id';
    public $incrementing = false;
    const CREATED_AT = null;

    protected $fillable = ['office_id', 'lift_slot_count'];
}
