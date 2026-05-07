<?php

use App\Livewire\Shop\CheckoutPage;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
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

function makeCartWithItem(int $quantity = 1, int $price = 120, int $stock = 5): array
{
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => $price,
    ]);
    InventoryItem::factory()->create([
        'product_variant_id' => $variant->id,
        'quantity' => $stock,
    ]);

    $cart = Cart::query()->create([
        'session_id' => session()->getId(),
        'status' => 'active',
    ]);

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variant->id,
        'quantity' => $quantity,
        'unit_price' => $price,
    ]);

    return [$variant, $cart];
}

test('paystack: pay-now creates a pending order and dispatches paystack:open without redirect', function () {
    [$variant] = makeCartWithItem(quantity: 2, price: 150, stock: 5);

    Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder')
        ->assertNoRedirect()
        ->assertSet('payNowFlowPhase', 'summary')
        ->assertDispatched('paystack:open', function (string $event, array $params) {
            return $params['publicKey'] === 'pk_test_dummy'
                && $params['email'] === 'paystack@example.com'
                && $params['currency'] === 'GHS'
                && $params['amount'] === (int) round((150 * 2 + 30) * 100)
                && str_starts_with((string) $params['reference'], 'SQ-');
        });

    $order = Order::query()->latest()->firstOrFail();
    expect($order->order_number)->toMatch('/^\d{6}$/');
    expect($order->status)->toBe('pending_payment');
    expect($order->payment_method)->toBe('pay_now');

    $payment = Payment::query()->where('order_id', $order->id)->firstOrFail();
    expect($payment->provider)->toBe('paystack');
    expect($payment->status)->toBe('pending');
    expect((string) $payment->amount)->toBe('330.00');

    expect($variant->inventoryItem->fresh()->quantity)->toBe(5);
    expect(Cart::query()->where('status', 'active')->count())->toBe(1);
});

test('paystack: verifyPayment finalizes the order on a successful Paystack response', function () {
    [$variant] = makeCartWithItem(quantity: 2, price: 150, stock: 5);

    $component = Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder');

    $payment = Payment::query()->where('provider', 'paystack')->firstOrFail();
    $reference = $payment->transaction_reference;
    $expectedAmount = (int) round((float) $payment->amount * 100);

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'success',
                'reference' => $reference,
                'amount' => $expectedAmount,
                'currency' => 'GHS',
            ],
        ]),
    ]);

    $component
        ->dispatch('paystack:callback', reference: $reference)
        ->assertRedirect(route('shop.orders.show', $payment->fresh()->order));

    Http::assertSent(fn ($request) => str_contains($request->url(), '/transaction/verify/'.urlencode($reference)));

    expect($payment->fresh()->status)->toBe('paid');
    expect($payment->fresh()->order->status)->toBe('placed');
    expect($variant->inventoryItem->fresh()->quantity)->toBe(3);
    expect(Cart::query()->where('status', 'active')->count())->toBe(0);
});

test('paystack: verifyPayment marks the payment failed when Paystack reports a non-success status', function () {
    [$variant] = makeCartWithItem(quantity: 2, price: 150, stock: 5);

    $component = Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder');

    $payment = Payment::query()->where('provider', 'paystack')->firstOrFail();
    $reference = $payment->transaction_reference;

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'failed',
                'reference' => $reference,
                'amount' => (int) round((float) $payment->amount * 100),
                'currency' => 'GHS',
            ],
        ]),
    ]);

    $component->dispatch('paystack:callback', reference: $reference);

    expect($payment->fresh()->status)->toBe('failed');
    expect($payment->fresh()->order->status)->toBe('pending_payment');
    expect($variant->inventoryItem->fresh()->quantity)->toBe(5);
});

test('paystack: cancelling the inline modal cancels the pending payment', function () {
    makeCartWithItem(quantity: 1, price: 200, stock: 5);

    $component = Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder');

    $payment = Payment::query()->where('provider', 'paystack')->firstOrFail();

    $component->dispatch('paystack:cancelled', reference: $payment->transaction_reference);

    expect($payment->fresh()->status)->toBe('pending');
    expect($payment->fresh()->order->status)->toBe('pending_payment');
    $component->assertSet('payNowFlowPhase', 'summary');
});

test('paystack: pay_now requires Paystack keys to be configured', function () {
    config()->set('services.paystack.public_key', null);
    config()->set('services.paystack.secret_key', null);

    makeCartWithItem();

    Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder')
        ->assertNotDispatched('paystack:open');

    expect(Order::query()->count())->toBe(0);
});

test('paystack: retryPaystackPayment issues a new reference and opens Paystack after a failed verify', function () {
    [$variant] = makeCartWithItem(quantity: 1, price: 200, stock: 5);

    $component = Livewire::test(CheckoutPage::class)
        ->set('name', 'Paystack User')
        ->set('email', 'paystack@example.com')
        ->set('phone', '0241234567')
        ->set('shipping_address', '123 Commerce Street, Accra')
        ->set('delivery_zone', 'greater_accra')
        ->set('payment_method', 'pay_now')
        ->call('placeOrder');

    $payment = Payment::query()->where('provider', 'paystack')->firstOrFail();
    $oldRef = $payment->transaction_reference;

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'failed',
                'reference' => $oldRef,
                'amount' => (int) round((float) $payment->amount * 100),
                'currency' => 'GHS',
            ],
        ]),
    ]);

    $component->dispatch('paystack:callback', reference: $oldRef);

    expect($payment->fresh()->status)->toBe('failed');

    Http::fake(); // clear for retry (no verify call on retry)

    $component
        ->call('retryPaystackPayment')
        ->assertDispatched('paystack:open', function (string $event, array $params) use ($oldRef) {
            $ref = (string) $params['reference'];
            $expected = (int) round((float) Payment::query()->where('provider', 'paystack')->first()->amount * 100);

            return str_starts_with($ref, 'SQ-')
                && $ref !== $oldRef
                && $params['amount'] === $expected;
        });

    expect(Payment::query()->where('provider', 'paystack')->value('transaction_reference'))->not->toBe($oldRef);
    expect($variant->inventoryItem->fresh()->quantity)->toBe(5);
});
