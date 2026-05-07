<section class="w-full">
    <x-admin.layout :heading="__('Admin Dashboard')" :subheading="__('Operational KPIs for your store')">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text>{{ __('Orders Today') }}</flux:text>
                <flux:heading size="xl">{{ $ordersToday }}</flux:heading>
            </div>
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text>{{ __('Revenue Today') }}</flux:text>
                <flux:heading size="xl">${{ number_format($revenueToday, 2) }}</flux:heading>
            </div>
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <flux:text>{{ __('Low Stock Items') }}</flux:text>
                <flux:heading size="xl">{{ $lowStockCount }}</flux:heading>
            </div>
        </div>
    </x-admin.layout>
</section>
