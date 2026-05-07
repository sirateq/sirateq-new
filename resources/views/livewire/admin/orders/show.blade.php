<section class="w-full">
    <x-admin.layout :heading="__('Order Detail')" :subheading="__('Review line items and update state')">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading>{{ $order->order_number }}</flux:heading>
            <flux:text>{{ $order->customer_name }} ({{ $order->customer_email }})</flux:text>
            <flux:text>{{ __('Total') }}: ${{ number_format((float) $order->total, 2) }}</flux:text>

            <form wire:submit="updateStatus" class="mt-4 flex items-end gap-2">
                <flux:select wire:model="status" :label="__('Status')">
                    <flux:select.option value="pending">pending</flux:select.option>
                    <flux:select.option value="placed">placed</flux:select.option>
                    <flux:select.option value="paid">paid</flux:select.option>
                    <flux:select.option value="shipped">shipped</flux:select.option>
                    <flux:select.option value="cancelled">cancelled</flux:select.option>
                </flux:select>
                <flux:button type="submit" variant="primary">{{ __('Update') }}</flux:button>
            </form>

            <flux:separator class="my-4" />

            <div class="space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between" wire:key="admin-order-item-{{ $item->id }}">
                        <flux:text>{{ $item->product_name }} ({{ $item->variant_name }}) x{{ $item->quantity }}</flux:text>
                        <flux:text>${{ number_format((float) $item->line_total, 2) }}</flux:text>
                    </div>
                @endforeach
            </div>
        </div>
    </x-admin.layout>
</section>
