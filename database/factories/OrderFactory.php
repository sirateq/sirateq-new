<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.fake()->unique()->numberBetween(100000, 999999),
            'status' => fake()->randomElement(['pending', 'placed', 'paid']),
            'subtotal' => 100,
            'discount_total' => 0,
            'total' => 100,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'shipping_address' => fake()->address(),
        ];
    }
}
