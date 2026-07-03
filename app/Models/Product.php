<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'description',
        'category',
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'brand',
        'sku',
        'tags',
        'weight',
        'dimensions',
        'warranty_information',
        'shipping_information',
        'availability_status',
        'return_policy',
        'minimum_order_quantity',
        'meta',
        'reviews',
        'thumbnail',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'external_id' => 'integer',
            'price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'rating' => 'decimal:2',
            'stock' => 'integer',
            'tags' => 'array',
            'weight' => 'decimal:2',
            'dimensions' => 'array',
            'minimum_order_quantity' => 'integer',
            'meta' => 'array',
            'reviews' => 'array',
            'images' => 'array',
        ];
    }
}
