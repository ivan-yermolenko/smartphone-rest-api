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

    public function test_it_can_get_a_single_product(): void
    {
        $product = Product::factory()->create([
            'title' => 'iPhone X',
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.title', 'iPhone X');
    }

    public function test_it_returns_404_when_product_not_found(): void
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404);
    }

    public function test_it_can_create_a_product(): void
    {
        $payload = [
            'title' => 'New Smartphone',
            'description' => 'A great new phone',
            'price' => 599.99,
            'stock' => 10,
            'brand' => 'BrandNew',
            'sku' => 'NEW-SMART-001',
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Smartphone')
            ->assertJsonPath('data.brand', 'BrandNew');

        $this->assertDatabaseHas('products', [
            'title' => 'New Smartphone',
            'sku' => 'NEW-SMART-001',
        ]);
    }

    public function test_it_validates_required_fields_when_creating(): void
    {
        $response = $this->postJson('/api/products');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'price']);
    }

    public function test_it_can_update_a_product(): void
    {
        $product = Product::factory()->create([
            'title' => 'Old Title',
            'price' => 100.00,
        ]);

        $payload = [
            'title' => 'Updated Title',
            'price' => 150.00,
        ];

        $response = $this->patchJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.price', 150);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Updated Title',
            'price' => 150.00,
        ]);
    }

    public function test_it_validates_unique_sku_ignoring_self_when_updating(): void
    {
        $product1 = Product::factory()->create(['sku' => 'SKU-001']);
        $product2 = Product::factory()->create(['sku' => 'SKU-002']);

        $response1 = $this->patchJson("/api/products/{$product1->id}", ['sku' => $product1->sku]);
        $response1->assertStatus(200);

        $response2 = $this->patchJson("/api/products/{$product1->id}", ['sku' => $product2->sku]);
        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    }

    public function test_it_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
