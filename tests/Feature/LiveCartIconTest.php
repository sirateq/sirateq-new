<?php

use App\Livewire\Shop\CartIcon;
use App\Livewire\Shop\ProductShow;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('starts with an empty cart icon', function (): void {
    Livewire::test(CartIcon::class)
        ->assertSet('itemCount', 0);
});

it('dispatches cart-updated to the CartIcon component when adding to cart', function (): void {
    $product = Product::factory()->create(['is_active' => true]);
    $variant = ProductVariant::factory()->for($product)->create(['price' => 25]);
    InventoryItem::factory()->for($variant, 'variant')->create(['quantity' => 10]);

    Livewire::test(ProductShow::class, ['product' => $product])
        ->set('quantity', 2)
        ->call('addToCart')
        ->assertDispatched('cart-updated');
});

it('refreshes the live cart icon count when cart-updated is dispatched', function (): void {
    $product = Product::factory()->create(['is_active' => true]);
    $variant = ProductVariant::factory()->for($product)->create(['price' => 25]);
    InventoryItem::factory()->for($variant, 'variant')->create(['quantity' => 10]);

    $icon = Livewire::test(CartIcon::class)
        ->assertSet('itemCount', 0);

    Livewire::test(ProductShow::class, ['product' => $product])
        ->set('quantity', 3)
        ->call('addToCart');

    $icon->dispatch('cart-updated')
        ->assertSet('itemCount', 3)
        ->assertSee('3');
});
