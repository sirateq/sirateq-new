<?php

namespace App\Actions\Shop;

use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Models\Order;
use App\Services\SmsDeliveryService;
use Illuminate\Support\Facades\Mail;

/**
 * Sends markdown email + SMS to the customer and configured admin recipients when an order is **placed** (fulfillment complete).
 */
class SendOrderPlacedNotifications
{
    public function __invoke(Order $order): void
    {
        if ($order->status !== 'placed') {
            return;
        }

        $this->sendCustomerNoticeOnly($order);
        $this->sendAdminNoticesOnly($order);
    }

    /**
     * Customer-facing order confirmation email + SMS (e.g. admin resend).
     */
    public function sendCustomerNoticeOnly(Order $order): void
    {
        $order->loadMissing(['items', 'payments', 'coupon']);

        $orderPageUrl = $order->temporarySignedStorefrontUrl();

        Mail::to($order->customer_email)->send(new OrderPlacedCustomerMail($order));

        $sms = app(SmsDeliveryService::class);

        if (filled($order->customer_phone)) {
            $sms->send(
                $order->customer_phone,
                __(':app: Hi :name, order #:num confirmed. Total GH₵:total. Order: :url Thank you!', [
                    'app' => config('app.name'),
                    'name' => $order->customer_name,
                    'num' => $order->order_number,
                    'total' => number_format((float) $order->total, 2),
                    'url' => $orderPageUrl,
                ])
            );
        }
    }

    public function sendAdminNoticesOnly(Order $order): void
    {
        $order->loadMissing(['items', 'payments', 'coupon']);
        $orderPageUrl = $order->temporarySignedStorefrontUrl();

        foreach (config('shop_notifications.admin_emails', []) as $email) {
            if (! filled($email)) {
                continue;
            }

            Mail::to($email)->send(new OrderPlacedAdminMail($order));
        }

        $sms = app(SmsDeliveryService::class);

        foreach (config('shop_notifications.admin_phone_numbers', []) as $phone) {
            if (! filled($phone)) {
                continue;
            }

            $sms->send(
                $phone,
                __(':app ADMIN: Order #:num — :name, GH₵:total. :email Order: :url', [
                    'app' => config('app.name'),
                    'num' => $order->order_number,
                    'name' => $order->customer_name,
                    'total' => number_format((float) $order->total, 2),
                    'email' => $order->customer_email,
                    'url' => $orderPageUrl,
                ])
            );
        }
    }
}
