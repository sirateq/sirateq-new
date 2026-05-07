<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product details')]
class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public string $selectedVariant = '';

    public function mount(Product $product): void
    {
        $this->product = $product->load('variants');
        $this->selectedVariant = (string) $this->product->variants->first()?->id;
    }

    public function addToCart(): void
    {
        $this->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'selectedVariant' => ['required', 'exists:product_variants,id'],
        ]);

        $variant = ProductVariant::query()->with('inventoryItem')->findOrFail($this->selectedVariant);

        if ((int) optional($variant->inventoryItem)->quantity < $this->quantity) {
            Flux::toast(variant: 'danger', text: __('Insufficient stock for this variant.'));

            return;
        }

        $cart = Cart::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'status' => 'active',
            ],
            [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'status' => 'active',
            ],
        );

        $cart->items()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'quantity' => $this->quantity,
                'unit_price' => $variant->price,
            ],
        );

        Flux::toast(variant: 'success', text: __('Item added to cart.'));
        $this->redirectRoute('shop.cart', navigate: true);
    }

    public function render()
    {
        return view('livewire.shop.product-show');
    }
}
