<section class="w-full">
    <x-admin.layout :heading="__('Categories')" :subheading="__('Manage catalog categories')">
        <div class="mb-4">
            <flux:button variant="primary" :href="route('admin.categories.create')" wire:navigate>{{ __('Add Category') }}</flux:button>
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

        <flux:separator class="my-6" />
        <flux:heading size="lg">{{ __('Trash') }}</flux:heading>

        <flux:table :paginate="$this->trashedCategories">
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Slug') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->trashedCategories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $category->slug }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button variant="ghost" :href="route('admin.categories.show', $category->id)" size="sm" wire:navigate>{{ __('View') }}</flux:button>
                                <flux:button variant="subtle" size="sm" wire:click="restore({{ $category->id }})">{{ __('Restore') }}</flux:button>
                                <flux:button variant="danger" size="sm" wire:click="destroy({{ $category->id }})">{{ __('Delete Permanently') }}</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell>{{ __('No trashed categories.') }}</flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                        <flux:table.cell></flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-admin.layout>
</section>
