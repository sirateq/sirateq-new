<section class="w-full">
    <x-admin.layout :heading="__('Orders')" :subheading="__('Track and manage customer orders')">
        <flux:table :paginate="$this->orders">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'order_number'" :direction="$sortDirection" wire:click="sort('order_number')">{{ __('Order') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'customer_email'" :direction="$sortDirection" wire:click="sort('customer_email')">{{ __('Customer') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'total'" :direction="$sortDirection" wire:click="sort('total')">{{ __('Total') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->orders as $order)
                    <flux:table.row :key="$order->id">
                        <flux:table.cell variant="strong">{{ $order->order_number }}</flux:table.cell>
                        <flux:table.cell>{{ $order->customer_email }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$order->status === 'paid' ? 'green' : 'zinc'" inset="top bottom">{{ strtoupper($order->status) }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>${{ number_format((float) $order->total, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="subtle" :href="route('admin.orders.show', $order)" size="sm" wire:navigate>{{ __('View') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-admin.layout>
</section>
