@php
    $statusColor = fn (string $status): string => match ($status) {
        'paid', 'shipped' => 'green',
        'placed' => 'blue',
        'pending_payment', 'pending' => 'amber',
        'cancelled' => 'red',
        default => 'zinc',
    };
@endphp

<section class="w-full">
    <x-admin.layout
        :heading="$user->name"
        :subheading="__(':email · :orders orders', ['email' => $user->email, 'orders' => $user->orders_count])"
        icon="users"
    >
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.customers.index')" wire:navigate>
                {{ __('Customers') }}
            </flux:button>
        </x-slot>

        <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900 sm:flex-row sm:items-center sm:gap-6">
            <flux:avatar size="xl" :name="$user->name" />
            <div class="min-w-0 flex-1 space-y-2">
                <flux:heading size="xl">{{ $user->name }}</flux:heading>
                <flux:text size="sm" class="text-zinc-500">{{ $user->email }}</flux:text>
                <div class="flex flex-wrap gap-2">
                    @if ($user->email_verified_at)
                        <flux:badge size="sm" color="green" inset="top bottom">{{ __('Verified') }}</flux:badge>
                    @else
                        <flux:badge size="sm" color="amber" inset="top bottom">{{ __('Unverified email') }}</flux:badge>
                    @endif
                    <flux:badge size="sm" color="zinc" inset="top bottom">{{ trans_choice(':count order|:count orders', $user->orders_count, ['count' => $user->orders_count]) }}</flux:badge>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200/70 px-5 py-4 dark:border-zinc-700/60">
                <flux:heading size="lg">{{ __('Orders') }}</flux:heading>
                <flux:subheading>{{ __('Linked account orders (registered checkout)') }}</flux:subheading>
            </div>

            <flux:table :paginate="$customerOrders">
                <flux:table.columns>
                    <flux:table.column>{{ __('Order') }}</flux:table.column>
                    <flux:table.column>{{ __('Placed') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Total') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($customerOrders as $order)
                        <flux:table.row :key="'cu-order-'.$order->id">
                            <flux:table.cell variant="strong" class="font-mono text-xs">{{ $order->order_number }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">{{ $order->created_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$statusColor($order->status)" inset="top bottom">
                                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell variant="strong" align="end" class="tabular-nums">GH₵{{ number_format((float) $order->total, 2) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:tooltip :content="__('View order')">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.orders.show', $order)" wire:navigate />
                                </flux:tooltip>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="receipt-percent" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No orders on this account') }}</flux:heading>
                                    <flux:text>{{ __('This customer has not completed a logged-in checkout yet.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
