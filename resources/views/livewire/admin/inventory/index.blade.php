<section class="w-full">
    <x-admin.layout :heading="__('Inventory')" :subheading="__('Adjust stock levels')">
        <flux:table :paginate="$this->items">
            <flux:table.columns>
                <flux:table.column>{{ __('Product') }}</flux:table.column>
                <flux:table.column>{{ __('SKU') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'quantity'" :direction="$sortDirection" wire:click="sort('quantity')">{{ __('Quantity') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->items as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell variant="strong">{{ $item->variant->product->name }} ({{ $item->variant->name }})</flux:table.cell>
                        <flux:table.cell>{{ $item->variant->sku }}</flux:table.cell>
                        <flux:table.cell>{{ $item->quantity }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button variant="subtle" size="sm" wire:click="adjust({{ $item->id }}, -1)">-1</flux:button>
                                <flux:button variant="subtle" size="sm" wire:click="adjust({{ $item->id }}, 1)">+1</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-admin.layout>
</section>
