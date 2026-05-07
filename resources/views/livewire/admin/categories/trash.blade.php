<section class="w-full">
    <x-admin.layout :heading="__('Categories Trash')" :subheading="__('Restore or permanently delete trashed categories')" icon="trash">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.categories.index')" wire:navigate>
                {{ __('Back to Categories') }}
            </flux:button>
        </x-slot>

        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <flux:table :paginate="$this->trashedCategories">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'deleted_at'" :direction="$sortDirection" wire:click="sort('deleted_at')">{{ __('Trashed') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->trashedCategories as $category)
                        <flux:table.row :key="$category->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-red-50 dark:bg-red-950/40">
                                        <flux:icon name="trash" class="size-4 text-red-500" />
                                    </div>
                                    <flux:heading class="!text-sm">{{ $category->name }}</flux:heading>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs">{{ $category->slug }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">{{ $category->deleted_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:tooltip :content="__('View')">
                                        <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.categories.show', $category->id)" wire:navigate />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Restore')">
                                        <flux:button variant="ghost" size="sm" icon="arrow-uturn-left" wire:click="restore({{ $category->id }})" />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Delete permanently')">
                                        <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="destroy({{ $category->id }})" wire:confirm="{{ __('This will permanently delete the category. Continue?') }}" />
                                    </flux:tooltip>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="check-circle" class="size-8 text-emerald-500" />
                                    <flux:heading>{{ __('Trash is empty') }}</flux:heading>
                                    <flux:text>{{ __('Trashed categories will appear here.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
