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
            return [
                'color' => $item['color'] ?? null,
                'images' => collect($item['images'] ?? [])->map(fn ($img) => r2_url($img))->toArray(),
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
            'image_primary' => r2_url($this->image_primary),
            'image_hover' => r2_url($this->image_hover),
            'size_chart_image' => r2_url($this->size_chart_image),
            'colors' => $colors,
            'material' => $this->material,
            'weight' => (float) $this->weight,
            'is_active' => (bool) $this->is_active,
            'variations' => ProductVariationResource::collection($this->whenLoaded('variations')),
            'total_stock' => $this->whenLoaded('variations', fn () => $this->variations->sum('stock'), 0),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
