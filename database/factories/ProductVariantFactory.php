<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOptionStructure;
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

    public function configure(): static
    {
        return $this->afterCreating(function (ProductVariant $variant): void {
            if ($variant->optionSelections()->exists()) {
                return;
            }
            $product = $variant->product()->first();
            if (! $product) {
                return;
            }
            ProductOptionStructure::attachVariantToOptionGroup(
                $product,
                $variant,
                'Option',
                $variant->name,
            );
        });
    }
}
