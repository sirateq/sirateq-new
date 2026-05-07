<?php

use App\Livewire\Admin\Products\Form as ProductForm;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin product form generates a unique SKU when SKU is left blank on create', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(ProductForm::class)
        ->assertSet('variants.0.quantity', 1000)
        ->set('name', 'Auto SKU Widget')
        ->set('category_id', $category->id)
        ->set('variants.0.name', 'Default')
        ->set('variants.0.sku', '')
        ->set('variants.0.price', 19.99)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('name', 'Auto SKU Widget')->firstOrFail();
    $variant = $product->variants->firstOrFail();

    expect($variant->sku)->toStartWith('SQ-')
        ->and(strlen($variant->sku))->toBeGreaterThan(5);

    expect($variant->inventoryItem)->not->toBeNull()
        ->and($variant->inventoryItem->quantity)->toBe(1000);
});

test('admin product form keeps existing SKU when the field is cleared on edit', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create(['name' => 'Existing Widget']);
    $variant = ProductVariant::factory()->for($product)->create([
        'name' => 'Default',
        'sku' => 'KEEP-THIS-SKU',
        'price' => 12,
    ]);
    InventoryItem::factory()->create([
        'product_variant_id' => $variant->id,
        'quantity' => 77,
    ]);

    Livewire::actingAs($admin)
        ->test(ProductForm::class, ['product' => $product])
        ->set('variants.0.sku', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($variant->fresh()->sku)->toBe('KEEP-THIS-SKU');
});

test('admin product form saves a custom SKU when provided', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(ProductForm::class)
        ->set('name', 'Custom SKU Widget')
        ->set('category_id', $category->id)
        ->set('variants.0.name', 'Default')
        ->set('variants.0.sku', 'MY-SKU-123')
        ->set('variants.0.price', 5)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('name', 'Custom SKU Widget')->firstOrFail();
    expect($product->variants->first()->sku)->toBe('MY-SKU-123');
});
