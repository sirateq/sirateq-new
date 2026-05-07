<section class="w-full">
    <x-admin.layout :heading="__('Categories')" :subheading="__('Organize your catalog into browsable groups')" icon="squares-2x2">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="trash" :href="route('admin.categories.trash')" wire:navigate>
                {{ __('Trash') }}
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('admin.categories.create')" wire:navigate>
                {{ __('Add Category') }}
            </flux:button>
        </x-slot>

        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200/70 p-4 dark:border-zinc-700/60">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search categories')" class="sm:max-w-xs" />
            </div>

            <flux:table :paginate="$this->categories">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    {{-- <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column> --}}
                    <flux:table.column>{{ __('Products') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->categories as $category)
                        <flux:table.row :key="$category->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="squares-2x2" class="size-4 text-zinc-500" />
                                    </div>
                                    <flux:heading class="!text-sm">{{ $category->name }}</flux:heading>
                                </div>
                            </flux:table.cell>
                            {{-- <flux:table.cell class="font-mono text-xs">{{ $category->slug }}</flux:table.cell> --}}
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $category->products_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$category->is_active ? 'green' : 'zinc'" :icon="$category->is_active ? 'check-circle' : 'minus-circle'" inset="top bottom">
                                    {{ $category->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:tooltip :content="__('View')">
                                        <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.categories.show', $category->id)" wire:navigate />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Edit')">
                                        <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('admin.categories.edit', $category)" wire:navigate />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Trash')">
                                        <flux:button variant="ghost" size="sm" icon="trash" wire:click="trash({{ $category->id }})" wire:confirm="{{ __('Move this category to trash?') }}" />
                                    </flux:tooltip>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="squares-2x2" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No categories found') }}</flux:heading>
                                    <flux:text>{{ __('Create one to start grouping your products.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
