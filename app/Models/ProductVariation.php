<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'product_id',
        'color',
        'size',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
