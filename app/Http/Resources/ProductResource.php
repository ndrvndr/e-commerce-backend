<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $colors = collect($this->color_images ?? [])->map(function ($item) {
            $imageUrls = collect($item['images'] ?? [])->map(function ($img) {
                return asset('storage/' . $img);
            })->toArray();

            return [
                'color' => $item['color'] ?? null,
                'images' => $imageUrls,
            ];
        });

        return [
            'id' => $this->id,
            'catalog' => new CatalogResource($this->whenLoaded('catalog')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'image_primary' => $this->image_primary ? asset('storage/' . $this->image_primary) : null,
            'image_hover' => $this->image_hover ? asset('storage/' . $this->image_hover) : null,
            'size_chart_image' => $this->size_chart_image ? asset('storage/' . $this->size_chart_image) : null,
            'colors' => $colors,
            'material' => $this->material,
            'weight' => (float) $this->weight,
            'is_active' => (bool) $this->is_active,
            'variations' => ProductVariationResource::collection($this->whenLoaded('variations')),
            'total_stock' => $this->whenLoaded('variations', fn() => $this->variations->sum('stock'), 0),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
