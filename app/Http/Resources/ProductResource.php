<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'price' => (float) $this->price,
            'in_stock' => $this->isInStock(),
            'available_sizes' => $this->availableSizes()->all(),
            'available_colors' => $this->availableColors()->all(),
            'images' => $this->images->map(fn ($image) => $image->url)->all(),
            'variants' => $this->variants->map(fn ($variant) => [
                'size' => $variant->size,
                'color' => $variant->color,
                'stock' => $variant->stock,
            ])->values()->all(),
        ];
    }
}
