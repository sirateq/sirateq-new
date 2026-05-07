<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\CartItem;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart')]
class CartPage extends Component
{
    public ?string $couponCode = null;

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::query()->findOrFail($itemId);
        $item->update(['quantity' => max(1, $quantity)]);
    }

    public function removeItem(int $itemId): void
    {
        CartItem::query()->findOrFail($itemId)->delete();
        Flux::toast(text: __('Item removed from cart.'));
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
        return view('livewire.shop.cart-page');
    }
}
