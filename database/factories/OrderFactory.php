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
            'coupon_id' => null,
            'order_number' => str_pad((string) fake()->unique()->numberBetween(0, 999_999), 6, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(['pending', 'placed', 'paid']),
            'subtotal' => 100,
            'discount_total' => 0,
            'delivery_fee' => 0,
            'total' => 100,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('02########'),
            'shipping_address' => fake()->address(),
            'delivery_zone' => 'Greater Accra',
            'payment_method' => 'pay_on_delivery',
        ];
    }
}
