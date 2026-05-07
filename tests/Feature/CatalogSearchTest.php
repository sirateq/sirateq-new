<?php

use App\Livewire\Shop\Catalog;
use App\Models\Category;
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
