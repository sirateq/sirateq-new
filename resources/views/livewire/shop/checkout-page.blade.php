<section class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Checkout') }}</flux:heading>
        <flux:subheading>{{ __('Submit your shipping details and place your order.') }}</flux:subheading>
    </div>

    @if (! $this->cart || $this->cart->items->isEmpty())
        <flux:callout icon="information-circle" variant="warning">{{ __('No cart found. Add products before checking out.') }}</flux:callout>
    @else
        <form wire:submit="placeOrder" class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-4">
                <flux:input wire:model="name" :label="__('Full name')" required />
                <flux:input wire:model="email" type="email" :label="__('Email address')" required />
                <flux:textarea wire:model="shipping_address" :label="__('Shipping address')" required />
                <flux:input wire:model="coupon_code" :label="__('Coupon code (optional)')" />
                <flux:button variant="primary" type="submit">{{ __('Place order') }}</flux:button>
            </div>
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:heading>{{ __('Order summary') }}</flux:heading>
                <div class="mt-3 space-y-2">
                    @foreach ($this->cart->items as $item)
                        <div class="flex items-center justify-between" wire:key="checkout-item-{{ $item->id }}">
                            <flux:text>{{ $item->variant->product->name }} x{{ $item->quantity }}</flux:text>
                            <flux:text>${{ number_format((float) $item->unit_price * $item->quantity, 2) }}</flux:text>
                        </div>
                    @endforeach
                </div>
                <flux:separator class="my-3" />
                <div class="flex items-center justify-between">
                    <flux:text>{{ __('Subtotal') }}</flux:text>
                    <flux:heading>${{ number_format($this->cart->subtotal(), 2) }}</flux:heading>
                </div>
            </div>
        </form>
    @endif
</section>
