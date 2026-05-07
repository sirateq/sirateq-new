<?php

use App\Livewire\Shop\OrderTracking;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('order tracking finds order by number and email and grants session', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'order_number' => '123456',
        'customer_email' => 'buyer@example.com',
        'status' => 'placed',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', '#123456')
        ->set('email', 'buyer@example.com')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toContain($order->id);
});

test('order tracking matches email case-insensitively', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'order_number' => '999888',
        'customer_email' => 'mixed@example.com',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', '999888')
        ->set('email', 'MIXED@EXAMPLE.COM')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toContain($order->id);
});

test('order tracking rejects mismatched email', function () {
    Order::factory()->create([
        'order_number' => '654321',
        'customer_email' => 'a@example.com',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', '654321')
        ->set('email', 'wrong@example.com')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toBe([]);
});
