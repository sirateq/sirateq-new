<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => fake()->numberBetween(0, 50),
            'low_stock_threshold' => 5,
        ];
    }
}
