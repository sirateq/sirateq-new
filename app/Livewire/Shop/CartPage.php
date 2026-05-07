<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Shopping Cart')]
class CartPage extends Component
{
    public const COUPON_SESSION_KEY = 'cart.coupon_code';

    public ?string $couponCode = null;

    public ?string $appliedCoupon = null;

    public float $appliedDiscount = 0.0;

    public function mount(): void
    {
        $stored = session()->get(self::COUPON_SESSION_KEY);

        if (filled($stored)) {
            $this->couponCode = $stored;
            $this->applyCoupon(silent: true);
        }
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::query()->findOrFail($itemId);
        $item->update(['quantity' => max(1, $quantity)]);

        $this->recalculateDiscount();
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $itemId): void
    {
        CartItem::query()->findOrFail($itemId)->delete();

        $this->recalculateDiscount();
        $this->dispatch('cart-updated');

        Flux::toast(text: __('Item removed from cart.'));
    }

    public function clearCart(): void
    {
        $this->cart?->items()->delete();
        $this->forgetCoupon();

        unset($this->cart);
        $this->dispatch('cart-updated');

        Flux::toast(text: __('Cart cleared.'));
    }

    public function applyCoupon(bool $silent = false): void
    {
        if (blank($this->couponCode)) {
            if (! $silent) {
                Flux::toast(variant: 'danger', text: __('Please enter a coupon code.'));
            }

            return;
        }

        $code = strtoupper(trim($this->couponCode));

        $coupon = Coupon::query()->where('code', $code)->first();

        if (! $coupon || ! $coupon->isCurrentlyActive()) {
            $this->forgetCoupon();

            if (! $silent) {
                Flux::toast(variant: 'danger', text: __('Invalid or expired coupon code.'));
            }

            return;
        }

        $subtotal = $this->cartSubtotal();

        if ($subtotal <= 0) {
            if (! $silent) {
                Flux::toast(variant: 'danger', text: __('Add items to your cart before applying a coupon.'));
            }

            return;
        }

        $this->couponCode = $code;
        $this->appliedCoupon = $code;
        $this->appliedDiscount = round(($subtotal * $coupon->discount_percentage) / 100, 2);

        session()->put(self::COUPON_SESSION_KEY, $code);

        if (! $silent) {
            Flux::toast(variant: 'success', text: __('Coupon applied — you saved GH₵:amount', [
                'amount' => number_format($this->appliedDiscount, 2),
            ]));
        }
    }

    public function removeCoupon(): void
    {
        $this->forgetCoupon();
        Flux::toast(text: __('Coupon removed.'));
    }

    public function recalculateDiscount(): void
    {
        if (! $this->appliedCoupon) {
            return;
        }

        $coupon = Coupon::query()->where('code', $this->appliedCoupon)->first();

        if (! $coupon || ! $coupon->isCurrentlyActive()) {
            $this->forgetCoupon();

            return;
        }

        $this->appliedDiscount = round(($this->cartSubtotal() * $coupon->discount_percentage) / 100, 2);
    }

    public function cartSubtotal(): float
    {
        return (float) ($this->cart?->subtotal() ?? 0);
    }

    public function total(): float
    {
        return max(0, $this->cartSubtotal() - $this->appliedDiscount);
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
            ->with('items.variant.product.images', 'items.variant.inventoryItem')
            ->latest()
            ->first();
    }

    protected function forgetCoupon(): void
    {
        $this->couponCode = null;
        $this->appliedCoupon = null;
        $this->appliedDiscount = 0.0;

        session()->forget(self::COUPON_SESSION_KEY);
    }

    public function render()
    {
        return view('livewire.shop.cart-page');
    }
}
