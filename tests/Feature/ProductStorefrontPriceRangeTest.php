<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows single storefront price when one variant or all prices match', function (): void {
    $product = Product::factory()->create();
    ProductVariant::factory()->for($product)->create(['price' => 99.5]);
    $product->load('variants');
    expect($product->storefrontVariantPriceLabel())->toBe('GH₵99.50');

    $uniform = Product::factory()->create();
    ProductVariant::factory()->for($uniform)->create(['price' => 40]);
    ProductVariant::factory()->for($uniform)->create(['price' => 40]);
    $uniform->load('variants');
    expect($uniform->storefrontVariantPriceLabel())->toBe('GH₵40.00');
});

it('shows min–max range when variant prices differ', function (): void {
    $product = Product::factory()->create();
    ProductVariant::factory()->for($product)->create(['price' => 40]);
    ProductVariant::factory()->for($product)->create(['price' => 200]);
    $product->load('variants');
    expect($product->storefrontVariantPriceLabel())->toBe('GH₵40.00 – 200.00');
});
