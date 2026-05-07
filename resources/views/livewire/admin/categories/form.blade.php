<section class="w-full">
    <x-admin.layout
        :heading="$category ? __('Edit Category') : __('New Category')"
        :subheading="__('Create or update a catalog category')"
        icon="squares-2x2">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.categories.index')" wire:navigate>
                {{ __('Back') }}
            </flux:button>
        </x-slot>

        <form wire:submit="save" class="space-y-6">
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <header class="mb-4 flex items-center gap-2">
                    <flux:icon name="information-circle" class="size-5 text-zinc-500" />
                    <flux:heading size="lg">{{ __('Details') }}</flux:heading>
                </header>
                <flux:separator class="mb-5" />

                <div class="grid gap-4 max-w-xl">
                    <flux:input wire:model="name" :label="__('Name')" :placeholder="__('e.g. Apparel')" required />
                    <flux:textarea wire:model="description" :label="__('Description')" :placeholder="__('Help shoppers understand what this group contains')" rows="4" />
                    <div class="rounded-lg border border-zinc-200/70 bg-zinc-50 p-4 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                        <flux:switch wire:model="is_active" :label="__('Visible on storefront')" :description="__('When disabled, products in this category remain hidden')" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse items-stretch justify-end gap-2 sm:flex-row sm:items-center">
                <flux:button variant="ghost" :href="route('admin.categories.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                    {{ $category ? __('Save changes') : __('Create category') }}
                </flux:button>
            </div>
        </form>
    </x-admin.layout>
</section>
