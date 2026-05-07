<?php

use App\Livewire\Admin\Orders\Show;
use App\Mail\AdminCustomCustomerNoticeMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can download order invoice PDF', function () {
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

    $this->actingAs($admin)
        ->get(route('admin.orders.invoice', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->tap(function (TestResponse $response): void {
            expect($response->getContent())->toContain(config('invoice.company.name'))
                ->and($response->getContent())->toContain('info@sirateqghana.com');
        });
});

test('guest cannot download admin order invoice', function () {
    $order = Order::factory()->create();

    $this->get(route('admin.orders.invoice', $order))->assertRedirect();
});

test('admin can send custom email to customer from order page', function () {
    Mail::fake();
    $admin = User::factory()->create(['is_admin' => true]);
    $order = Order::factory()->create([
        'customer_email' => 'cust@example.com',
    ]);

    Livewire::actingAs($admin)
        ->test(Show::class, ['order' => $order])
        ->set('customEmailSubject', 'Hello from us')
        ->set('customEmailBody', 'Your **order** is ready.')
        ->call('sendCustomCustomerEmail');

    Mail::assertSent(AdminCustomCustomerNoticeMail::class, function (AdminCustomCustomerNoticeMail $mail): bool {
        return $mail->noticeSubject === 'Hello from us'
            && $mail->markdownBody === 'Your **order** is ready.';
    });
});
