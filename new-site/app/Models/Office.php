<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Office extends Model
{
    protected $fillable = ['title', 'shipping'];

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function mobilePrefs(): HasOne
    {
        return $this->hasOne(OfficeMobilePref::class);
    }
}
