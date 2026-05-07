<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('signed storefront link grants customer access and redirects to order page', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'status' => 'placed',
    ]);

    $url = URL::temporarySignedRoute(
        'shop.orders.signed-show',
        now()->addHour(),
        ['order' => $order],
    );

    $this->get($url)->assertRedirect(route('shop.orders.show', $order));

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY))->toContain($order->id);
});

test('signed invoice link grants customer access and redirects to invoice download', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'status' => 'placed',
    ]);

    $url = URL::temporarySignedRoute(
        'shop.orders.signed-invoice',
        now()->addHour(),
        ['order' => $order],
    );

    $this->get($url)->assertRedirect(route('shop.orders.invoice', $order));

    expect(session()->get(Order::CUSTOMER_SESSION_ORDER_IDS_KEY))->toContain($order->id);
});

test('invalid signature rejects signed order link', function () {
    $order = Order::factory()->create(['user_id' => null, 'status' => 'placed']);

    $this->get(route('shop.orders.signed-show', $order))->assertForbidden();
});
