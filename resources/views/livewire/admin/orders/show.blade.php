<section class="w-full">
    <x-admin.layout :heading="$order->order_number" :subheading="__('Review line items and update fulfilment status')" icon="receipt-percent">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.orders.index')" wire:navigate>
                {{ __('Back') }}
            </flux:button>
        </x-slot>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <header class="mb-4 flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">{{ __('Line items') }}</flux:heading>
                            <flux:subheading>{{ $order->items->count() }} {{ __('items') }}</flux:subheading>
                        </div>
                        <flux:badge size="sm" :color="match ($order->status) { 'paid', 'shipped' => 'green', 'placed' => 'blue', 'pending' => 'amber', 'cancelled' => 'red', default => 'zinc' }" inset="top bottom">
                            {{ ucfirst($order->status) }}
                        </flux:badge>
                    </header>
                    <flux:separator />

                    <ul class="divide-y divide-zinc-200/70 dark:divide-zinc-700/60">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between gap-4 py-3" wire:key="admin-order-item-{{ $item->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="cube" class="size-4 text-zinc-500" />
                                    </div>
                                    <div>
                                        <flux:heading class="!text-sm">{{ $item->product_name }}</flux:heading>
                                        <flux:text size="sm" class="text-zinc-500">{{ $item->variant_name }} · x{{ $item->quantity }}</flux:text>
                                    </div>
                                </div>
                                <flux:text variant="strong">GH₵{{ number_format((float) $item->line_total, 2) }}</flux:text>
                            </li>
                        @endforeach
                    </ul>

                    <flux:separator class="my-4" />
                    <div class="flex items-center justify-between">
                        <flux:heading>{{ __('Total') }}</flux:heading>
                        <flux:heading size="lg">GH₵{{ number_format((float) $order->total, 2) }}</flux:heading>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-3">{{ __('Customer') }}</flux:heading>
                    <flux:separator class="mb-4" />
                    <div class="flex items-center gap-3">
                        <flux:avatar :name="$order->customer_name ?? $order->customer_email" />
                        <div>
                            <flux:heading class="!text-sm">{{ $order->customer_name ?: __('Guest') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500">{{ $order->customer_email }}</flux:text>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-3">{{ __('Update status') }}</flux:heading>
                    <flux:separator class="mb-4" />
                    <form wire:submit="updateStatus" class="space-y-3">
                        <flux:select wire:model="status" :label="__('Status')">
                            <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                            <flux:select.option value="placed">{{ __('Placed') }}</flux:select.option>
                            <flux:select.option value="paid">{{ __('Paid') }}</flux:select.option>
                            <flux:select.option value="shipped">{{ __('Shipped') }}</flux:select.option>
                            <flux:select.option value="cancelled">{{ __('Cancelled') }}</flux:select.option>
                        </flux:select>
                        <flux:button type="submit" variant="primary" icon="check" class="w-full" wire:loading.attr="disabled">
                            {{ __('Save status') }}
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>
    </x-admin.layout>
</section>
