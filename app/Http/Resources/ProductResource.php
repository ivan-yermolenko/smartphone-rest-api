<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'price' => (float) $this->price,
            'discount_percentage' => (float) $this->discount_percentage,
            'rating' => (float) $this->rating,
            'stock' => $this->stock,
            'brand' => $this->brand,
            'sku' => $this->sku,
            'tags' => $this->tags,
            'weight' => (float) $this->weight,
            'dimensions' => $this->dimensions,
            'warranty_information' => $this->warranty_information,
            'shipping_information' => $this->shipping_information,
            'availability_status' => $this->availability_status,
            'return_policy' => $this->return_policy,
            'minimum_order_quantity' => $this->minimum_order_quantity,
            'meta' => $this->meta,
            'reviews' => $this->reviews,
            'thumbnail' => $this->thumbnail,
            'images' => $this->images,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
