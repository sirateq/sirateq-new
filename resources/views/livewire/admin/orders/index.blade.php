<section class="w-full">
    <x-admin.layout :heading="__('Orders')" :subheading="__('Track and manage customer orders')" icon="receipt-percent">
        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-200/70 p-4 dark:border-zinc-700/60 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by order # or customer')" class="sm:max-w-xs" />
                    <flux:select wire:model.live="status" class="sm:max-w-xs">
                        <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
                        <flux:select.option value="pending_payment">{{ __('Awaiting payment') }}</flux:select.option>
                        <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                        <flux:select.option value="placed">{{ __('Placed') }}</flux:select.option>
                        <flux:select.option value="paid">{{ __('Paid') }}</flux:select.option>
                        <flux:select.option value="shipped">{{ __('Shipped') }}</flux:select.option>
                        <flux:select.option value="cancelled">{{ __('Cancelled') }}</flux:select.option>
                    </flux:select>
                </div>
                <flux:button variant="outline" icon="arrow-down-tray" :href="route('admin.exports.orders', [
                    'q' => $this->search,
                    'status' => $this->status,
                    'sort_by' => $this->sortBy,
                    'sort_direction' => $this->sortDirection,
                ])">
                    {{ __('Export Excel') }}
                </flux:button>
            </div>

            <flux:table :paginate="$this->orders">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'order_number'" :direction="$sortDirection" wire:click="sort('order_number')">{{ __('Order') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'customer_email'" :direction="$sortDirection" wire:click="sort('customer_email')">{{ __('Customer') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Placed') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'total'" :direction="$sortDirection" wire:click="sort('total')" align="end">{{ __('Total') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->orders as $order)
                        <flux:table.row :key="$order->id">
                            <flux:table.cell variant="strong" class="font-mono text-xs">{{ $order->order_number }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="xs" :name="$order->customer_name ?? $order->customer_email" />
                                    <div>
                                        <flux:heading class="!text-sm">{{ $order->customer_name ?: __('Guest') }}</flux:heading>
                                        <flux:text size="sm" class="text-zinc-500">{{ $order->customer_email }}</flux:text>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">{{ $order->created_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$this->statusColor($order->status)" inset="top bottom">
                                    {{ ucfirst($order->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell variant="strong" align="end">GH₵{{ number_format((float) $order->total, 2) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:tooltip :content="__('View')">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.orders.show', $order)" wire:navigate />
                                </flux:tooltip>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="receipt-percent" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No orders yet') }}</flux:heading>
                                    <flux:text>{{ __('Orders placed from the storefront will appear here.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
