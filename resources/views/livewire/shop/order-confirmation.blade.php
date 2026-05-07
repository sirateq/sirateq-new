<section class="space-y-6">
    <flux:callout icon="check-circle" variant="success">
        {{ __('Order placed successfully.') }}
    </flux:callout>

    <div class="rounded-lg border border-zinc-200 p-6 dark:border-zinc-700">
        <flux:heading size="lg">{{ __('Order #:number', ['number' => $order->order_number]) }}</flux:heading>
        <flux:text>{{ __('Status: :status', ['status' => strtoupper($order->status)]) }}</flux:text>
        <flux:text>{{ __('Total: $:amount', ['amount' => number_format((float) $order->total, 2)]) }}</flux:text>

        <flux:separator class="my-4" />

        <div class="space-y-2">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between" wire:key="order-item-{{ $item->id }}">
                    <flux:text>{{ $item->product_name }} ({{ $item->variant_name }}) x{{ $item->quantity }}</flux:text>
                    <flux:text>${{ number_format((float) $item->line_total, 2) }}</flux:text>
                </div>
            @endforeach
        </div>
    </div>
</section>
