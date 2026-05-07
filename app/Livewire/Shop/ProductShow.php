<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Product details')]
class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public string $selectedVariant = '';

    public ?string $activeImage = null;

    public function mount(Product $product): void
    {
        $this->product = $product->load(['variants.inventoryItem', 'images', 'category']);
        $this->selectedVariant = (string) $this->product->variants->first()?->id;
        $this->activeImage = $this->product->main_image_url;
    }

    public function setActiveImage(string $url): void
    {
        $this->activeImage = $url;
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariant = (string) $variantId;
        unset($this->cartItem);
    }

    public function addToCart(): void
    {
        $this->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'selectedVariant' => ['required', 'exists:product_variants,id'],
        ]);

        $variant = ProductVariant::query()->with('inventoryItem')->findOrFail($this->selectedVariant);
        $stock = (int) optional($variant->inventoryItem)->quantity;

        $cart = $this->resolveOrCreateCart();
        $existing = $cart->items()->where('product_variant_id', $variant->id)->first();
        $existingQty = (int) ($existing?->quantity ?? 0);
        $newQty = $existingQty + $this->quantity;

        if ($newQty > $stock) {
            $remaining = max(0, $stock - $existingQty);

            Flux::toast(
                variant: 'danger',
                text: $remaining === 0
                    ? __('You already have the max available (:n) in your cart.', ['n' => $existingQty])
                    : __('Only :remaining more can be added (:in_cart already in your cart).', [
                        'remaining' => $remaining,
                        'in_cart' => $existingQty,
                    ]),
            );

            return;
        }

        if ($existing) {
            $existing->update([
                'quantity' => $newQty,
                'unit_price' => $variant->price,
            ]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $newQty,
                'unit_price' => $variant->price,
            ]);
        }

        $this->quantity = 1;
        unset($this->cartItem);
        $this->dispatch('cart-updated');

        Flux::toast(variant: 'success', text: __('Item added to cart.'));
    }

    public function incrementCartQuantity(): void
    {
        $item = $this->cartItem;

        if (! $item) {
            return;
        }

        $stock = (int) optional($item->variant->inventoryItem)->quantity;

        if ($item->quantity >= $stock) {
            Flux::toast(variant: 'danger', text: __('Reached stock limit.'));

            return;
        }

        $item->increment('quantity');
        unset($this->cartItem);
        $this->dispatch('cart-updated');
    }

    public function decrementCartQuantity(): void
    {
        $item = $this->cartItem;

        if (! $item) {
            return;
        }

        if ($item->quantity <= 1) {
            $item->delete();
        } else {
            $item->decrement('quantity');
        }

        unset($this->cartItem);
        $this->dispatch('cart-updated');
    }

    public function removeFromCart(): void
    {
        $this->cartItem?->delete();

        unset($this->cartItem);
        $this->dispatch('cart-updated');

        Flux::toast(text: __('Removed from cart.'));
    }

    #[Computed]
    public function cartItem(): ?CartItem
    {
        if (blank($this->selectedVariant)) {
            return null;
        }

        $cart = Cart::query()
            ->where('status', 'active')
            ->where('session_id', session()->getId())
            ->where(fn ($query) => $query
                ->where('user_id', Auth::id())
                ->orWhereNull('user_id'))
            ->latest()
            ->first();

        if (! $cart) {
            return null;
        }

        return $cart->items()
            ->with('variant.inventoryItem')
            ->where('product_variant_id', (int) $this->selectedVariant)
            ->first();
    }

    #[On('cart-updated')]
    public function refreshCartState(): void
    {
        unset($this->cartItem);
    }

    protected function resolveOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(
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
    }

    public function render()
    {
        return view('livewire.shop.product-show');
    }
}
