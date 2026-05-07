<?php

use App\Livewire\Shop\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('services.paystack.public_key', 'pk_test_dummy');
    config()->set('services.paystack.secret_key', 'sk_test_dummy');
    config()->set('services.paystack.currency', 'GHS');
    config()->set('services.paystack.base_url', 'https://api.paystack.co');
});

test('invoice download returns 404 without customer access', function () {
    $order = Order::factory()->create(['user_id' => null]);

    $this->get(route('shop.orders.invoice', $order))->assertNotFound();
});

test('invoice download succeeds for session-granted guest order', function () {
    $order = Order::factory()->create(['user_id' => null]);
    Order::grantCustomerSessionAccess($order);

    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'Test Product',
        'variant_name' => 'Default',
        'quantity' => 1,
        'unit_price' => 100,
        'line_total' => 100,
    ]);

    $response = $this->get(route('shop.orders.invoice', $order));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('pdf');
});

test('invoice download succeeds when authenticated user owns the order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'Owned',
        'variant_name' => 'Default',
        'quantity' => 1,
        'unit_price' => 50,
        'line_total' => 50,
    ]);

    $this->actingAs($user)
        ->get(route('shop.orders.invoice', $order))
        ->assertOk();
});

test('order confirmation page returns 404 without customer access', function () {
    $order = Order::factory()->create(['user_id' => null]);

    $this->get(route('shop.orders.show', $order))->assertNotFound();
});

test('order confirmation allows paystack verification for pending payment order', function () {
    $order = Order::factory()->create([
        'user_id' => null,
        'status' => 'pending_payment',
        'payment_method' => 'pay_now',
        'subtotal' => 100,
        'discount_total' => 0,
        'delivery_fee' => 30,
        'total' => 130,
    ]);
    Order::grantCustomerSessionAccess($order);

    $payment = Payment::query()->create([
        'order_id' => $order->id,
        'provider' => 'paystack',
        'status' => 'pending',
        'amount' => 130,
        'transaction_reference' => 'SQ-TESTREF123',
    ]);

    $expectedAmount = (int) round((float) $payment->amount * 100);

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'success',
                'reference' => $payment->transaction_reference,
                'amount' => $expectedAmount,
                'currency' => 'GHS',
            ],
        ]),
    ]);

    Livewire::test(OrderConfirmation::class, ['order' => $order])
        ->call('verifyLatestPaystackPayment')
        ->assertHasNoErrors();

    expect($payment->fresh()->status)->toBe('paid');
    expect($order->fresh()->status)->toBe('placed');
});
