<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class DummyJsonService
{
    private const string API_URL = 'https://dummyjson.com/products/category/smartphones';

    /**
     * @return int Number of processed products
     *
     * @throws \RuntimeException if the API request fails
     */
    public function importProducts(): int
    {
        $response = Http::get(self::API_URL);

        if ($response->failed()) {
            Log::error('Failed to fetch from DummyJSON API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to fetch products from external API');
        }

        $data = $response->json();
        $products = $data['products'] ?? [];
        $upsertData = [];

        foreach ($products as $item) {
            $upsertData[] = [
                'external_id' => $item['id'],
                'title' => $item['title'],
                'description' => $item['description'],
                'category' => $item['category'] ?? 'smartphones',
                'price' => $item['price'],
                'discount_percentage' => $item['discountPercentage'] ?? null,
                'rating' => $item['rating'] ?? null,
                'stock' => $item['stock'] ?? 0,
                'brand' => $item['brand'] ?? null,
                'sku' => $item['sku'] ?? null,
                'tags' => json_encode($item['tags'] ?? []),
                'weight' => $item['weight'] ?? null,
                'dimensions' => json_encode($item['dimensions'] ?? null),
                'warranty_information' => $item['warrantyInformation'] ?? null,
                'shipping_information' => $item['shippingInformation'] ?? null,
                'availability_status' => $item['availabilityStatus'] ?? null,
                'return_policy' => $item['returnPolicy'] ?? null,
                'minimum_order_quantity' => $item['minimumOrderQuantity'] ?? null,
                'meta' => json_encode($item['meta'] ?? null),
                'reviews' => json_encode($item['reviews'] ?? []),
                'thumbnail' => $item['thumbnail'] ?? null,
                'images' => json_encode($item['images'] ?? []),
            ];
        }

        if ($upsertData !== []) {
            Product::upsert(
                $upsertData,
                ['external_id'],
                [
                    'title', 'description', 'category', 'price', 'discount_percentage',
                    'rating', 'stock', 'brand', 'sku', 'tags', 'weight', 'dimensions',
                    'warranty_information', 'shipping_information', 'availability_status',
                    'return_policy', 'minimum_order_quantity', 'meta', 'reviews',
                    'thumbnail', 'images',
                ]
            );
        }

        return count($upsertData);
    }
}
