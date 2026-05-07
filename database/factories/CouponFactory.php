<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SAVE##')),
            'name' => fake()->words(2, true),
            'discount_percentage' => fake()->numberBetween(5, 40),
            'usage_limit' => null,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ];
    }
}
