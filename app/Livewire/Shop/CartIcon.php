<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    #[Computed]
    public function itemCount(): int
    {
        $cart = Cart::query()
            ->where('status', 'active')
            ->where('session_id', session()->getId())
            ->where(fn ($query) => $query
                ->where('user_id', Auth::id())
                ->orWhereNull('user_id'))
            ->with('items:id,cart_id,quantity')
            ->latest()
            ->first();

        return (int) ($cart?->items->sum('quantity') ?? 0);
    }

    #[On('cart-updated')]
    public function refresh(): void
    {
        unset($this->itemCount);
    }

    public function render()
    {
        return view('livewire.shop.cart-icon');
    }
}
