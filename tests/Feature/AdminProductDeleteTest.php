<?php

use App\Livewire\Admin\Products\Index as ProductIndex;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can delete a product that has never been ordered', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create();
    ProductVariant::factory()->create(['product_id' => $product->id]);

    Livewire::actingAs($admin)
        ->test(ProductIndex::class)
        ->call('destroy', $product->id);

    expect(Product::query()->find($product->id))->toBeNull();
});

test('admin cannot delete a product that appears on an order', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
    $order = Order::factory()->create(['user_id' => null]);
    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'X',
        'variant_name' => 'Y',
        'quantity' => 1,
        'unit_price' => 10,
        'line_total' => 10,
    ]);

    Livewire::actingAs($admin)
        ->test(ProductIndex::class)
        ->call('destroy', $product->id);

    expect(Product::query()->find($product->id))->not->toBeNull();
});
