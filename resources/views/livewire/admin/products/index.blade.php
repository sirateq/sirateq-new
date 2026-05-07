<section class="w-full">
    <x-admin.layout :heading="__('Products')" :subheading="__('Manage catalog products and visibility')">
        <div class="mb-4">
            <flux:button variant="primary" :href="route('admin.products.create')" wire:navigate>{{ __('Add Product') }}</flux:button>
        </div>
        <flux:table :paginate="$this->products">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Category') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->products as $product)
                    <flux:table.row :key="$product->id">
                        <flux:table.cell variant="strong">{{ $product->name }}</flux:table.cell>
                        <flux:table.cell>{{ $product->category->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$product->is_active ? 'green' : 'zinc'" inset="top bottom">
                                {{ $product->is_active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button variant="subtle" :href="route('admin.products.edit', $product)" size="sm" wire:navigate>{{ __('Edit') }}</flux:button>
                                <flux:button variant="ghost" size="sm" wire:click="toggleStatus({{ $product->id }})">
                                    {{ $product->is_active ? __('Disable') : __('Enable') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-admin.layout>
</section>
