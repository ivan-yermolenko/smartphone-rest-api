<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $external_id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string $price
 * @property string|null $discount_percentage
 * @property string|null $rating
 * @property int $stock
 * @property string|null $brand
 * @property string|null $sku
 * @property array|null $tags
 * @property string|null $weight
 * @property array|null $dimensions
 * @property string|null $warranty_information
 * @property string|null $shipping_information
 * @property string|null $availability_status
 * @property string|null $return_policy
 * @property int|null $minimum_order_quantity
 * @property array|null $meta
 * @property array|null $reviews
 * @property string|null $thumbnail
 * @property array|null $images
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Product ofBrand(?string $brand)
 */
final class Product extends Model
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

    public function scopeOfBrand(Builder $query, ?string $brand): Builder
    {
        return $query->when($brand, fn (Builder $q) => $q->where('brand', $brand));
    }
}
