<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Js;
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

    /** @var array<int, int> group id => product_option_value id */
    public array $selectedValueByGroupId = [];

    public ?string $activeImage = null;

    public function mount(Product $product): void
    {
        $this->product = $product->load([
            'variants.inventoryItem',
            'variants.optionSelections',
            'images',
            'category',
            'optionGroups.values.productImage',
        ]);

        $first = $this->product->variants->first();
        $this->selectedVariant = (string) $first?->id;

        $this->selectedValueByGroupId = [];
        if ($first) {
            foreach ($this->product->optionGroups as $group) {
                $sel = $first->optionSelections->firstWhere('product_option_group_id', $group->id);
                if ($sel) {
                    $this->selectedValueByGroupId[$group->id] = $sel->product_option_value_id;
                }
            }
        }

        $this->activeImage = $this->product->main_image_url;
        $this->syncGalleryFromSelectedOptions();
    }

    public function setActiveImage(string $url): void
    {
        $this->activeImage = $url;
    }

    public function selectOptionValue(int $groupId, int $valueId): void
    {
        $this->selectedValueByGroupId[$groupId] = $valueId;

        $variant = $this->resolveVariantFromOptionSelection();
        if ($variant) {
            $this->selectedVariant = (string) $variant->id;
        }

        $this->syncGalleryFromSelectedOptions();
        unset($this->cartItem);
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariant = (string) $variantId;
        $variant = $this->product->variants->firstWhere('id', $variantId);
        if ($variant) {
            foreach ($this->product->optionGroups as $group) {
                $s = $variant->optionSelections->firstWhere('product_option_group_id', $group->id);
                if ($s) {
                    $this->selectedValueByGroupId[$group->id] = $s->product_option_value_id;
                }
            }
        }
        $this->syncGalleryFromSelectedOptions();
        unset($this->cartItem);
    }

    /**
     * Whether any in-stock variant matches this hypothetical choice for the given group.
     */
    public function isOptionValueAvailable(int $groupId, int $valueId): bool
    {
        $this->product->loadMissing(['variants.inventoryItem', 'variants.optionSelections']);

        $partial = $this->selectedValueByGroupId;
        $partial[$groupId] = $valueId;

        foreach ($this->product->variants as $variant) {
            $stock = (int) optional($variant->inventoryItem)->quantity;
            if ($stock < 1) {
                continue;
            }
            $matches = true;
            foreach ($this->product->optionGroups as $group) {
                $expected = $partial[$group->id] ?? null;
                if ($expected === null) {
                    $matches = false;
                    break;
                }
                $row = $variant->optionSelections->firstWhere('product_option_group_id', $group->id);
                if ((int) ($row?->product_option_value_id) !== (int) $expected) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                return true;
            }
        }

        return false;
    }

    protected function resolveVariantFromOptionSelection(): ?ProductVariant
    {
        foreach ($this->product->variants as $variant) {
            $ok = true;
            foreach ($this->product->optionGroups as $group) {
                $expected = $this->selectedValueByGroupId[$group->id] ?? null;
                if ($expected === null) {
                    $ok = false;
                    break;
                }
                $row = $variant->optionSelections->firstWhere('product_option_group_id', $group->id);
                if ((int) ($row?->product_option_value_id) !== (int) $expected) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return $variant;
            }
        }

        return null;
    }

    protected function syncGalleryFromSelectedOptions(): void
    {
        foreach ($this->product->optionGroups as $group) {
            $vid = $this->selectedValueByGroupId[$group->id] ?? null;
            if (! $vid) {
                continue;
            }
            $value = $group->values->firstWhere('id', $vid);
            if ($value?->product_image_id) {
                $value->loadMissing('productImage');
                if ($value->productImage) {
                    $this->activeImage = $value->productImage->url;

                    return;
                }
            }
        }

        $this->activeImage = $this->product->main_image_url;
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

    /**
     * @return Collection<int, Product>
     */
    #[Computed]
    public function youMayAlsoLike(): Collection
    {
        $limit = 4;

        $sameCategory = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $this->product->id)
            ->where('category_id', $this->product->category_id)
            ->with(['variants.inventoryItem', 'images', 'category'])
            ->latest()
            ->limit($limit)
            ->get();

        if ($sameCategory->count() >= $limit) {
            return collect($sameCategory->all());
        }

        $excludeIds = $sameCategory->pluck('id')->push($this->product->id)->all();

        $more = Product::query()
            ->where('is_active', true)
            ->whereNotIn('id', $excludeIds)
            ->with(['variants.inventoryItem', 'images', 'category'])
            ->latest()
            ->limit($limit - $sameCategory->count())
            ->get();

        return collect($sameCategory->concat($more)->all());
    }

    public function copyShareLink(): void
    {
        $url = route('shop.products.show', $this->product, absolute: true);

        $this->js(
            'navigator.clipboard.writeText('.Js::from($url).').then('
            .'() => $wire.afterShareLinkCopied(),'
            .'() => $wire.shareLinkCopyFailed());'
        );
    }

    public function afterShareLinkCopied(): void
    {
        Flux::toast(variant: 'success', text: __('Link copied to clipboard.'));
    }

    public function shareLinkCopyFailed(): void
    {
        Flux::toast(variant: 'danger', text: __('Could not copy the link. Try again or use a share button below.'));
    }

    public function openNativeShare(): void
    {
        $url = Js::from(route('shop.products.show', $this->product, absolute: true));
        $title = Js::from($this->product->name);

        $this->js(
            <<<JS
                (async () => {
                    if (! navigator.share) {
                        \$wire.nativeShareFallback();

                        return;
                    }

                    try {
                        await navigator.share({ title: {$title}, text: {$title}, url: {$url} });
                    } catch (e) {
                        if (e && e.name !== 'AbortError') {
                            \$wire.nativeShareFallback();
                        }
                    }
                })();
                JS
        );
    }

    public function nativeShareFallback(): void
    {
        Flux::toast(text: __('Copy the link or use a social icon below.'));
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
        $shareUrl = route('shop.products.show', $this->product, absolute: true);

        return view('livewire.shop.product-show', [
            'shareUrl' => $shareUrl,
            'shareUrlEnc' => rawurlencode($shareUrl),
            'shareTitleEnc' => rawurlencode($this->product->name),
            'whatsappTextEnc' => rawurlencode($this->product->name."\n".$shareUrl),
            'mailtoSubjectEnc' => rawurlencode($this->product->name),
            'mailtoBodyEnc' => rawurlencode($shareUrl),
        ]);
    }
}
