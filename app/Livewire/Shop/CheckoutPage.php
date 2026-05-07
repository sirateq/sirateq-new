<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class CheckoutPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $shipping_address = '';

    public ?string $coupon_code = null;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;
        }
    }

    public function placeOrder(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'shipping_address' => ['required', 'string', 'min:10'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
        ]);

        $cart = $this->cart;

        if (! $cart || $cart->items->isEmpty()) {
            Flux::toast(variant: 'danger', text: __('Your cart is empty.'));

            return;
        }

        DB::transaction(function () use ($cart, $validated): void {
            $subtotal = (float) $cart->subtotal();
            $discountTotal = 0;
            $coupon = null;

            if (! empty($validated['coupon_code'])) {
                $coupon = Coupon::query()->where('code', strtoupper($validated['coupon_code']))->first();

                if ($coupon && $coupon->isCurrentlyActive()) {
                    $discountTotal = round(($subtotal * $coupon->discount_percentage) / 100, 2);
                }
            }

            $total = max(0, $subtotal - $discountTotal);

            $order = Order::query()->create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'status' => 'placed',
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total' => $total,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($cart->items as $item) {
                $variant = ProductVariant::query()->with('inventoryItem', 'product')->findOrFail($item->product_variant_id);
                $lineTotal = (float) $item->unit_price * $item->quantity;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $lineTotal,
                ]);

                if ($variant->inventoryItem) {
                    $variant->inventoryItem->decrement('quantity', $item->quantity);
                }
            }

            Payment::query()->create([
                'order_id' => $order->id,
                'provider' => 'manual',
                'status' => 'paid',
                'amount' => $total,
                'transaction_reference' => 'TRX-'.strtoupper((string) str()->random(10)),
            ]);

            if ($coupon && $discountTotal > 0) {
                $coupon->increment('used_count');
                $coupon->redemptions()->create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ]);
            }

            $cart->items()->delete();
            $cart->update(['status' => 'converted']);

            $this->redirectRoute('shop.orders.show', ['order' => $order], navigate: true);
        });
    }

    #[Computed]
    public function cart(): ?Cart
    {
        return Cart::query()
            ->where('status', 'active')
            ->where('session_id', session()->getId())
            ->where(fn ($query) => $query
                ->where('user_id', Auth::id())
                ->orWhereNull('user_id'))
            ->with('items.variant.product')
            ->latest()
            ->first();
    }

    public function render()
    {
        return view('livewire.shop.checkout-page');
    }
}
