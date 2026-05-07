<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['Standard', 'Premium']),
            'sku' => strtoupper(fake()->bothify('SKU-####-??')),
            'price' => fake()->randomFloat(2, 10, 999),
            'is_active' => true,
        ];
    }
}
