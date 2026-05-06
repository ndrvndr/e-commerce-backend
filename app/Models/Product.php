<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'catalog_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'image_primary',
        'image_hover',
        'size_chart_image',
        'color_images',
        'material',
        'weight',
        'brand',
        'is_active',
    ];

    protected $casts = [
        'color_images' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($product) {
            $fields = ['image_primary', 'image_hover', 'size_chart_image'];
            foreach ($fields as $field) {
                if ($product->isDirty($field) && $product->getOriginal($field)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->getOriginal($field));
                }
            }
            
            if ($product->isDirty('color_images')) {
                $oldImages = collect($product->getOriginal('color_images') ?? [])->pluck('images')->flatten()->filter();
                $newImages = collect($product->color_images ?? [])->pluck('images')->flatten()->filter();
                $toDelete = $oldImages->diff($newImages);
                foreach ($toDelete as $img) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
                }
            }
        });

        static::deleting(function ($product) {
            $fields = ['image_primary', 'image_hover', 'size_chart_image'];
            foreach ($fields as $field) {
                if ($product->$field) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->$field);
                }
            }

            if ($product->color_images) {
                $imagesToDelete = collect($product->color_images)->pluck('images')->flatten()->filter();
                foreach ($imagesToDelete as $img) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
                }
            }
        });
    }
}
