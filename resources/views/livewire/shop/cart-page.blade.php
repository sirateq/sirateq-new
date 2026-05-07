<section class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Your Cart') }}</flux:heading>
        <flux:subheading>{{ __('Review your items before checkout.') }}</flux:subheading>
    </div>

    @if (! $this->cart || $this->cart->items->isEmpty())
        <flux:callout icon="shopping-cart" variant="warning">{{ __('Your cart is currently empty.') }}</flux:callout>
    @else
        <div class="space-y-3">
            @foreach ($this->cart->items as $item)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700" wire:key="cart-item-{{ $item->id }}">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading>{{ $item->variant->product->name }}</flux:heading>
                            <flux:text>{{ $item->variant->name }} - ${{ number_format((float) $item->unit_price, 2) }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button variant="subtle" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">-</flux:button>
                            <flux:text>{{ $item->quantity }}</flux:text>
                            <flux:button variant="subtle" wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</flux:button>
                            <flux:button variant="danger" wire:click="removeItem({{ $item->id }})">{{ __('Remove') }}</flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between">
            <flux:heading>{{ __('Subtotal:') }} ${{ number_format($this->cart->subtotal(), 2) }}</flux:heading>
            <flux:button variant="primary" :href="route('shop.checkout')" wire:navigate>{{ __('Proceed to checkout') }}</flux:button>
        </div>
    @endif
</section>
