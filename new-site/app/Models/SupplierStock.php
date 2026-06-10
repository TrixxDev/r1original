<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierStock extends Model
{
    protected $table = 'supplier_stock';

    protected $fillable = ['product_id', 'supplier', 'article', 'quantity', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
