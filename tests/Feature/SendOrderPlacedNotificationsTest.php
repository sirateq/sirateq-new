<?php

use App\Actions\Shop\SendOrderPlacedNotifications;
use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SmsDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('send order placed notifications sends markdown mailables and sms log entries', function () {
    Mail::fake();
    Log::spy();

    config()->set('services.sms.driver', 'log');
    config()->set('shop_notifications.admin_emails', ['admin-one@test.com', 'admin-two@test.com']);
    config()->set('shop_notifications.admin_phone_numbers', ['0241111111']);

    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    $order = Order::factory()->create([
        'user_id' => null,
        'status' => 'placed',
        'customer_phone' => '0242222222',
        'customer_email' => 'buyer@test.com',
    ]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'Widget',
        'variant_name' => 'Standard',
        'quantity' => 1,
        'unit_price' => 99,
        'line_total' => 99,
    ]);

    $order->refresh();

    (new SendOrderPlacedNotifications)($order);

    Mail::assertSent(OrderPlacedCustomerMail::class, function (OrderPlacedCustomerMail $mail) use ($order): bool {
        return $mail->order->is($order) && count($mail->attachments()) === 1;
    });
    Mail::assertSent(OrderPlacedAdminMail::class, function (OrderPlacedAdminMail $mail) use ($order): bool {
        return $mail->order->is($order) && count($mail->attachments()) === 1;
    });
    Log::shouldHaveReceived('info')
        ->with('SMS notification (log driver)', Mockery::on(function (array $context): bool {
            return isset($context['message'], $context['to'])
                && str_contains($context['message'], 'Order: ');
        }))
        ->twice();
});

test('send order placed notifications skips email and sms when order is not placed', function () {
    Mail::fake();
    Log::spy();

    $order = Order::factory()->create([
        'status' => 'pending_payment',
        'payment_method' => 'pay_now',
    ]);

    (new SendOrderPlacedNotifications)($order);

    Mail::assertNothingSent();
    Log::shouldNotHaveReceived('info');
});

test('sms log driver records one log line', function () {
    Log::spy();
    config()->set('services.sms.driver', 'log');

    app(SmsDeliveryService::class)->send('0241000000', 'Test SMS body');

    Log::shouldHaveReceived('info')
        ->once()
        ->with('SMS notification (log driver)', Mockery::subset([
            'to' => '0241000000',
            'message' => 'Test SMS body',
        ]));
});

test('sms sendazi driver requests Sendazi quick campaign endpoint', function () {
    config()->set('services.sms.driver', 'sendazi');
    config()->set('services.sms.api_key', 'test-sendazi-key');
    config()->set('services.sms.sender_id', 'SIRATEQ');
    config()->set('services.sms.campaign_name', 'Order alerts');

    Http::fake([
        'https://sendazi.com/*' => Http::response(['success' => true], 200),
    ]);

    app(SmsDeliveryService::class)->send('0241000000', 'Test body');

    Http::assertSent(function (Request $request): bool {
        if (! str_contains($request->url(), 'https://sendazi.com/api/v1/sms-campaigns/quick')) {
            return false;
        }
        if (($request->header('Authorization')[0] ?? '') !== 'Bearer test-sendazi-key') {
            return false;
        }
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return ($query['to'] ?? null) === '0241000000'
            && ($query['message'] ?? null) === 'Test body';
    });
});

test('sms sendazi without api key falls back to log driver', function () {
    Log::spy();
    config()->set('services.sms.driver', 'sendazi');
    config()->set('services.sms.api_key', null);

    Http::fake();

    app(SmsDeliveryService::class)->send('0241000000', 'Fallback');

    Http::assertNothingSent();
    Log::shouldHaveReceived('info')
        ->once()
        ->with('SMS notification (log driver)', Mockery::type('array'));
});
