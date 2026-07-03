<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_product_and_cast_attributes_correctly(): void
    {
        $product = Product::factory()->create([
            'title' => 'Test Smartphone',
            'price' => 999.99,
            'tags' => ['apple', 'smartphone'],
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Test Smartphone',
            'price' => '999.99',
        ]);

        $savedProduct = Product::find($product->id);

        // Перевіряємо, що касти відпрацювали правильно
        $this->assertIsArray($savedProduct->tags);
        $this->assertEquals(['apple', 'smartphone'], $savedProduct->tags);

        $this->assertIsArray($savedProduct->dimensions);
        $this->assertIsArray($savedProduct->meta);
        $this->assertIsArray($savedProduct->reviews);
        $this->assertIsArray($savedProduct->images);
    }
}
