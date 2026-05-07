<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('non admin cannot export orders excel', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('admin.exports.orders'))->assertForbidden();
});

test('admin can export orders as excel', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $order = Order::factory()->create([
        'user_id' => null,
        'order_number' => '100200',
        'customer_email' => 'buyer@example.com',
    ]);
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'Widget',
        'variant_name' => 'Default',
        'quantity' => 2,
        'unit_price' => 10,
        'line_total' => 20,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.exports.orders'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('attachment');
    $body = $response->streamedContent();
    expect(substr($body, 0, 2))->toBe('PK');
});

test('admin can export products as excel', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Product::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.exports.products'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('attachment');
    $body = $response->streamedContent();
    expect(substr($body, 0, 2))->toBe('PK');
});

test('admin can download product import template', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get(route('admin.products.import.template'));

    $response->assertOk();
    $body = $response->streamedContent();
    expect(substr($body, 0, 2))->toBe('PK');
});

test('admin can import products from csv with name and category', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Category::factory()->create(['name' => 'Electronics']);

    $csv = "name,category\nImported Widget,Electronics\n";
    $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

    $this->actingAs($admin)->post(route('admin.products.import'), [
        'file' => $file,
    ])->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('name', 'Imported Widget')->with('variants')->first();
    expect($product)->not->toBeNull();
    expect($product->category->name)->toBe('Electronics');
    expect($product->variants)->toHaveCount(1);
    expect($product->variants->first()->sku)->toBe('IMP-'.$product->id);
    expect((float) $product->variants->first()->price)->toBe(0.0);
});

test('product import matches category case insensitively', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Category::factory()->create(['name' => 'Gadgets']);

    $csv = "name,category\nCool Gadget,GADGETS\n";
    $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

    $this->actingAs($admin)->post(route('admin.products.import'), ['file' => $file])
        ->assertRedirect(route('admin.products.index'));

    expect(Product::query()->where('name', 'Cool Gadget')->exists())->toBeTrue();
});

test('product import skips rows with unknown category', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $csv = "name,category\nMystery,NopeCategory\n";
    $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

    $this->actingAs($admin)->post(route('admin.products.import'), ['file' => $file])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('import_errors');

    expect(Product::query()->where('name', 'Mystery')->exists())->toBeFalse();
});

test('product import validates uploaded file', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.products.import'), [])
        ->assertSessionHasErrors('file');
});

test('orders export respects search filter', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Order::factory()->create(['customer_email' => 'unique-alpha@example.com']);
    Order::factory()->create(['customer_email' => 'unique-beta@example.com']);

    $response = $this->actingAs($admin)->get(route('admin.exports.orders', ['q' => 'unique-alpha']));
    $response->assertOk();
    $content = $response->streamedContent();
    expect(str_contains($content, 'unique-alpha@example.com'))->toBeTrue();
    expect(str_contains($content, 'unique-beta@example.com'))->toBeFalse();
});
