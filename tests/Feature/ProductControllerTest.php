<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Resource not found',
            ]);
    }

    public function test_it_returns_405_when_method_not_allowed(): void
    {
        $response = $this->postJson('/api/products/1');

        $response->assertStatus(405)
            ->assertJson([
                'success' => false,
                'error' => 'Method not allowed',
            ]);
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

    public function test_it_can_seed_products_from_dummyjson_api(): void
    {
        Http::fake([
            'https://dummyjson.com/products/category/smartphones' => Http::response([
                'products' => [
                    [
                        'id' => 121,
                        'title' => 'iPhone 5s Dummy',
                        'description' => 'A classic phone',
                        'category' => 'smartphones',
                        'price' => 199.99,
                        'discountPercentage' => 12.91,
                        'rating' => 2.83,
                        'stock' => 25,
                        'brand' => 'Apple',
                        'sku' => 'SMA-APP-IPH-121',
                        'tags' => ['smartphones', 'apple'],
                        'weight' => 2,
                        'dimensions' => [
                            'width' => 5.29,
                            'height' => 18.38,
                            'depth' => 17.72,
                        ],
                        'warrantyInformation' => '1 month warranty',
                        'shippingInformation' => 'Ships in 1 week',
                        'availabilityStatus' => 'In Stock',
                        'returnPolicy' => 'No returns',
                        'minimumOrderQuantity' => 1,
                        'meta' => [
                            'createdAt' => '2024-05-23T08:56:21.618Z',
                            'updatedAt' => '2024-05-23T08:56:21.618Z',
                            'barcode' => '123456789',
                            'qrCode' => 'qrcode-url',
                        ],
                        'reviews' => [],
                        'thumbnail' => 'thumbnail-url',
                        'images' => ['image-url'],
                    ],
                ],
                'total' => 1,
                'skip' => 0,
                'limit' => 1,
            ]),
        ]);

        $response = $this->postJson('/api/products/seed');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('imported', 1);

        $this->assertDatabaseHas('products', [
            'external_id' => 121,
            'title' => 'iPhone 5s Dummy',
            'sku' => 'SMA-APP-IPH-121',
            'brand' => 'Apple',
        ]);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://dummyjson.com/products/category/smartphones'
                && $request->method() === 'GET';
        });
    }

    public function test_it_handles_api_failure_during_seed(): void
    {
        Http::fake([
            'https://dummyjson.com/products/category/smartphones' => Http::response([], 500),
        ]);

        $response = $this->postJson('/api/products/seed');

        $response->assertStatus(502)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error', 'Failed to fetch products from external API');

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://dummyjson.com/products/category/smartphones'
                && $request->method() === 'GET';
        });
    }
}
