<?php

namespace App\Services;

use App\Actions\Shop\SendOrderPlacedNotifications;
use App\Livewire\Shop\CartPage;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    /**
     * Pay on delivery: order is already `placed`; decrement stock, redeem coupon, convert cart.
     */
    public function finalizePayOnDeliveryOrder(Order $order): void
    {
        if ($order->payment_method !== 'pay_on_delivery' || $order->status !== 'placed') {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->refresh();
            $order->load('items');

            foreach ($order->items as $item) {
                $variant = ProductVariant::query()->with('inventoryItem')->find($item->product_variant_id);
                $variant?->inventoryItem?->decrement('quantity', $item->quantity);
            }

            if ($order->coupon_id && (float) $order->discount_total > 0) {
                $coupon = Coupon::query()->find($order->coupon_id);
                if ($coupon && ! $coupon->redemptions()->where('order_id', $order->id)->exists()) {
                    $coupon->increment('used_count');
                    $coupon->redemptions()->create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                    ]);
                }
            }

            $cart = Cart::query()
                ->where('status', 'active')
                ->where('session_id', session()->getId())
                ->where(fn ($query) => $query
                    ->where('user_id', $order->user_id)
                    ->orWhereNull('user_id'))
                ->latest()
                ->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->update(['status' => 'converted']);
            }

            session()->forget(CartPage::COUPON_SESSION_KEY);

            $orderId = $order->id;
            DB::afterCommit(function () use ($orderId): void {
                $placed = Order::query()->find($orderId);
                if ($placed?->status === 'placed') {
                    app(SendOrderPlacedNotifications::class)($placed);
                }
            });
        });
    }

    /**
     * After online payment is confirmed: decrement stock, redeem coupon once,
     * convert active cart, clear coupon session, set order to placed.
     */
    public function finalizePaidOrder(Order $order): void
    {
        if ($order->status !== 'pending_payment') {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->refresh();
            $order->load('items');

            foreach ($order->items as $item) {
                $variant = ProductVariant::query()->with('inventoryItem')->find($item->product_variant_id);
                $variant?->inventoryItem?->decrement('quantity', $item->quantity);
            }

            if ($order->coupon_id && (float) $order->discount_total > 0) {
                $coupon = Coupon::query()->find($order->coupon_id);
                if ($coupon && ! $coupon->redemptions()->where('order_id', $order->id)->exists()) {
                    $coupon->increment('used_count');
                    $coupon->redemptions()->create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                    ]);
                }
            }

            $cart = Cart::query()
                ->where('status', 'active')
                ->where('session_id', session()->getId())
                ->where(fn ($query) => $query
                    ->where('user_id', $order->user_id)
                    ->orWhereNull('user_id'))
                ->latest()
                ->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->update(['status' => 'converted']);
            }

            session()->forget(CartPage::COUPON_SESSION_KEY);

            $order->update(['status' => 'placed']);

            $orderId = $order->id;
            DB::afterCommit(function () use ($orderId): void {
                $placed = Order::query()->find($orderId);
                if ($placed?->status === 'placed') {
                    app(SendOrderPlacedNotifications::class)($placed);
                }
            });
        });
    }
}
