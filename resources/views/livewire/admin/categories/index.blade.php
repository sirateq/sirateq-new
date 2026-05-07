<section class="w-full">
    <x-admin.layout :heading="__('Categories')" :subheading="__('Manage catalog categories')">
        <div class="mb-4 flex gap-2">
            <flux:button variant="primary" :href="route('admin.categories.create')" wire:navigate>{{ __('Add Category') }}</flux:button>
            <flux:button variant="subtle" icon="trash" :href="route('admin.categories.trash')" wire:navigate>{{ __('Trash') }}</flux:button>
        </div>
        <flux:table :paginate="$this->categories">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $category->slug }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button variant="ghost" :href="route('admin.categories.show', $category->id)" size="sm" wire:navigate>{{ __('View') }}</flux:button>
                                <flux:button variant="subtle" :href="route('admin.categories.edit', $category)" size="sm" wire:navigate>{{ __('Edit') }}</flux:button>
                                <flux:button variant="danger" size="sm" wire:click="trash({{ $category->id }})">{{ __('Trash') }}</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-admin.layout>
</section>
