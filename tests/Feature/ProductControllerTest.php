<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_products_with_pagination(): void
    {
        Product::factory()->count(20)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'external_id',
                        'title',
                        'description',
                        'category',
                        'price',
                        'brand',
                    ],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);

        $this->assertCount(15, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    public function test_it_can_filter_products_by_brand(): void
    {
        Product::factory()->count(3)->create(['brand' => 'Apple']);
        Product::factory()->count(2)->create(['brand' => 'Samsung']);

        $response = $this->getJson('/api/products?brand=Apple');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));

        foreach ($response->json('data') as $product) {
            $this->assertEquals('Apple', $product['brand']);
        }
    }

    public function test_it_returns_empty_when_brand_not_found(): void
    {
        Product::factory()->count(3)->create(['brand' => 'Apple']);

        $response = $this->getJson('/api/products?brand=Sony');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }
}
