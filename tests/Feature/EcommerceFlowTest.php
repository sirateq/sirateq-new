<?php

use App\Livewire\Shop\CheckoutPage;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('shop catalog page loads', function () {
    $response = $this->get(route('shop.index'));

    $response->assertSuccessful()
        ->assertSee('id="shop-search"', false);
});

test('checkout places order and decrements stock', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 120,
    ]);
    InventoryItem::factory()->create([
        'product_variant_id' => $variant->id,
        'quantity' => 5,
    ]);

    $cart = Cart::query()->create([
        'session_id' => session()->getId(),
        'status' => 'active',
    ]);

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variant->id,
        'quantity' => 2,
        'unit_price' => 120,
    ]);

    Livewire::test(CheckoutPage::class)
        ->set('name', 'Checkout User')
        ->set('email', 'checkout@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_on_delivery')
        ->call('placeOrder')
        ->assertRedirect(route('shop.orders.show', Order::query()->latest()->first()));

    expect($variant->inventoryItem->fresh()->quantity)->toBe(3);

    $order = Order::query()->latest()->first();
    expect((float) $order->delivery_fee)->toBe(0.0);
    expect((float) $order->total)->toBe(240.0);
});

test('checkout applies coupon discount', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 100,
    ]);
    InventoryItem::factory()->create([
        'product_variant_id' => $variant->id,
        'quantity' => 10,
    ]);
    Coupon::factory()->create([
        'code' => 'SAVE20',
        'discount_percentage' => 20,
    ]);

    $cart = Cart::query()->create([
        'session_id' => session()->getId(),
        'status' => 'active',
    ]);

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
        'unit_price' => 100,
    ]);

    Livewire::test(CheckoutPage::class)
        ->set('name', 'Coupon User')
        ->set('email', 'coupon@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_on_delivery')
        ->set('coupon_code', 'SAVE20')
        ->call('placeOrder')
        ->assertRedirect(route('shop.orders.show', Order::query()->latest()->first()));

    $order = Order::query()->latest()->first();
    expect($order->discount_total)->toBe('20.00');
    expect((float) $order->delivery_fee)->toBe(0.0);
    expect((float) $order->total)->toBe(80.0);
});
