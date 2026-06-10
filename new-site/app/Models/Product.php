<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const CATEGORIES = ['auto', 'moto', 'quadr', 'big', 'rim', 'quadrim', 'stud'];

    protected $fillable = [
        'model_id', 'category', 'article', 'width', 'profile', 'diameter', 'extra_dim',
        'load_index', 'speed_index', 'price_retail', 'price_partner', 'price_extra',
        'is_offer', 'is_price_offer', 'offer_price', 'offer_text', 'is_top', 'is_used',
        'visible_users', 'visible_list', 'available', 'comment', 'admin_comment',
        'code', 'attrs', 'ordered', 'reserved', 'legacy_source',
    ];

    protected $casts = [
        'attrs' => 'array',
        'is_offer' => 'bool',
        'is_price_offer' => 'bool',
        'is_top' => 'bool',
        'is_used' => 'bool',
        'available' => 'bool',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'model_id');
    }

    public function supplierStock(): HasMany
    {
        return $this->hasMany(SupplierStock::class);
    }
}
