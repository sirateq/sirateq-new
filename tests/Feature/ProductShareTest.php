<?php

use App\Livewire\Shop\ProductShow;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows you may also like and omits the shop search bar', function (): void {
    $category = Category::factory()->create();
    $main = Product::factory()->for($category)->create(['name' => 'Main Product', 'is_active' => true]);
    $sibling = Product::factory()->for($category)->create(['name' => 'Sibling Product', 'is_active' => true]);

    $mainVariant = ProductVariant::factory()->for($main)->create();
    $siblingVariant = ProductVariant::factory()->for($sibling)->create();
    InventoryItem::factory()->for($mainVariant, 'variant')->create(['quantity' => 5]);
    InventoryItem::factory()->for($siblingVariant, 'variant')->create(['quantity' => 5]);

    Livewire::test(ProductShow::class, ['product' => $main])
        ->assertSee(__('You may also like'))
        ->assertSee('Sibling Product')
        ->assertDontSee('product-show-shop-search', false);
});

it('renders product share links with the canonical product URL', function (): void {
    $product = Product::factory()->create(['is_active' => true]);
    $variant = ProductVariant::factory()->for($product)->create();
    InventoryItem::factory()->for($variant, 'variant')->create(['quantity' => 5]);

    $shareUrl = route('shop.products.show', $product, absolute: true);

    Livewire::test(ProductShow::class, ['product' => $product])
        ->assertSee('facebook.com/sharer/sharer.php', false)
        ->assertSee('twitter.com/intent/tweet', false)
        ->assertSee('linkedin.com/sharing/share-offsite', false)
        ->assertSee('api.whatsapp.com/send', false)
        ->assertSee('mailto:?', false)
        ->assertSee(rawurlencode($shareUrl), false)
        ->assertSee('wire:click="copyShareLink"', false)
        ->assertSee('wire:click="openNativeShare"', false);
});
