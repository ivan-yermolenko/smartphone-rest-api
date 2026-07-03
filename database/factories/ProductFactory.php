<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->randomNumber(5),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category' => 'smartphones',
            'price' => $this->faker->randomFloat(2, 50, 2000),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 20),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'stock' => $this->faker->numberBetween(0, 100),
            'brand' => $this->faker->company(),
            'sku' => $this->faker->unique()->uuid(),
            'tags' => ['smartphones', 'electronics'],
            'weight' => $this->faker->randomFloat(2, 100, 500),
            'dimensions' => [
                'width' => $this->faker->randomFloat(2, 5, 10),
                'height' => $this->faker->randomFloat(2, 10, 20),
                'depth' => $this->faker->randomFloat(2, 0.5, 2),
            ],
            'warranty_information' => '1 year warranty',
            'shipping_information' => 'Ships in 1-2 business days',
            'availability_status' => 'In Stock',
            'return_policy' => '30 days return policy',
            'minimum_order_quantity' => 1,
            'meta' => [
                'barcode' => $this->faker->ean13(),
                'qrCode' => $this->faker->imageUrl(),
            ],
            'reviews' => [
                [
                    'rating' => 5,
                    'comment' => 'Great phone!',
                    'reviewerName' => $this->faker->name(),
                ]
            ],
            'thumbnail' => $this->faker->imageUrl(),
            'images' => [
                $this->faker->imageUrl(),
                $this->faker->imageUrl(),
            ],
        ];
    }
}
