<section class="w-full">
    <x-admin.layout :heading="__('Dashboard')" :subheading="__('Operational KPIs for your store')" icon="chart-bar">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-zinc-500">{{ __('Orders today') }}</flux:text>
                        <flux:heading size="xl" class="mt-2">{{ $ordersToday }}</flux:heading>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                        <flux:icon name="shopping-bag" class="size-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-zinc-500">{{ __('Revenue today') }}</flux:text>
                        <flux:heading size="xl" class="mt-2">GH₵{{ number_format($revenueToday, 2) }}</flux:heading>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                        <flux:icon name="banknotes" class="size-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-zinc-500">{{ __('Low-stock items') }}</flux:text>
                        <flux:heading size="xl" class="mt-2">{{ $lowStockCount }}</flux:heading>
                    </div>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                        <flux:icon name="exclamation-triangle" class="size-5" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <header class="flex items-center justify-between gap-2 border-b border-zinc-200/70 p-4 dark:border-zinc-700/60">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="size-5 text-zinc-500" />
                    <flux:heading size="lg">{{ __('Recent orders') }}</flux:heading>
                </div>
                <flux:button variant="ghost" size="sm" icon="arrow-right" :href="route('admin.orders.index')" wire:navigate>
                    {{ __('View all') }}
                </flux:button>
            </header>

            @if ($recentOrders->isEmpty())
                <div class="flex flex-col items-center gap-2 py-10 text-center">
                    <flux:icon name="receipt-percent" class="size-8 text-zinc-400" />
                    <flux:heading>{{ __('No recent orders') }}</flux:heading>
                </div>
            @else
                <ul class="divide-y divide-zinc-200/70 dark:divide-zinc-700/60">
                    @foreach ($recentOrders as $order)
                        <li class="flex items-center justify-between gap-4 px-4 py-3" wire:key="recent-order-{{ $order->id }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <flux:avatar size="xs" :name="$order->customer_name ?? $order->customer_email" />
                                <div class="min-w-0">
                                    <flux:heading class="!text-sm">
                                        <span class="font-mono text-xs text-zinc-500">{{ $order->order_number }}</span>
                                        <span class="ml-2">{{ $order->customer_name ?: __('Guest') }}</span>
                                    </flux:heading>
                                    <flux:text size="sm" class="truncate text-zinc-500">{{ $order->customer_email }}</flux:text>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <flux:badge size="sm" :color="match ($order->status) { 'paid', 'shipped' => 'green', 'placed' => 'blue', 'pending' => 'amber', 'cancelled' => 'red', default => 'zinc' }" inset="top bottom">
                                    {{ ucfirst($order->status) }}
                                </flux:badge>
                                <flux:text variant="strong">GH₵{{ number_format((float) $order->total, 2) }}</flux:text>
                                <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.orders.show', $order)" wire:navigate />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </x-admin.layout>
</section>
