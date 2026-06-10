<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'category', 'title', 'slug', 'image', 'description', 'sort_order', 'legacy_source',
    ];

    public function productModels(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }
}
