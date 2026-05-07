<?php

use App\Livewire\Shop\OrderTracking;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('order tracking finds order by number only and grants session', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'order_number' => '123456',
        'customer_email' => 'buyer@example.com',
        'status' => 'placed',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', '#123456')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toContain($order->id);
});

test('order tracking strips hash prefix from order number', function () {
    $order = Order::factory()->create([
        'order_number' => 'ORD-99',
        'customer_email' => 'a@example.com',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', '#ORD-99')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toContain($order->id);
});

test('order tracking rejects unknown order number', function () {
    Order::factory()->create([
        'order_number' => '654321',
        'customer_email' => 'a@example.com',
    ]);

    Livewire::test(OrderTracking::class)
        ->set('order_number', 'does-not-exist')
        ->call('lookup');

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY, []))->toBe([]);
});
