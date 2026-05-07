<?php

use App\Livewire\Shop\Catalog;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('shop catalog filters products by search query', function () {
    $category = Category::factory()->create();

    $alpha = Product::factory()->for($category)->create([
        'name' => 'Alpha Widget',
        'is_active' => true,
    ]);
    ProductVariant::factory()->for($alpha)->create(['name' => 'Default', 'sku' => 'SKU-ALPHA', 'price' => 10]);

    $beta = Product::factory()->for($category)->create([
        'name' => 'Beta Gadget',
        'is_active' => true,
    ]);
    ProductVariant::factory()->for($beta)->create(['name' => 'Default', 'sku' => 'SKU-BETA', 'price' => 20]);

    Livewire::test(Catalog::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha Widget')
        ->assertDontSee('Beta Gadget');
});

test('shop catalog finds product by variant SKU', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create(['name' => 'Hidden Name', 'is_active' => true]);
    ProductVariant::factory()->for($product)->create([
        'name' => 'Size L',
        'sku' => 'UNIQUE-SKU-XYZ',
        'price' => 15,
    ]);

    Livewire::test(Catalog::class)
        ->set('search', 'unique-sku-xyz')
        ->assertSee('Hidden Name');
});

test('shop catalog shows out of stock when no variant has inventory', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Zero Stock Widget',
        'is_active' => true,
    ]);
    $variant = ProductVariant::factory()->for($product)->create(['price' => 50]);
    InventoryItem::factory()->for($variant, 'variant')->create(['quantity' => 0]);

    Livewire::test(Catalog::class)
        ->assertSee('Zero Stock Widget')
        ->assertSee(__('Out of stock'));
});

test('shop catalog hides out of stock badge when a variant is in stock', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Stocked Widget',
        'is_active' => true,
    ]);
    $variant = ProductVariant::factory()->for($product)->create(['price' => 50]);
    InventoryItem::factory()->for($variant, 'variant')->create(['quantity' => 10]);

    Livewire::test(Catalog::class)
        ->assertSee('Stocked Widget')
        ->assertDontSee(__('Out of stock'));
});
