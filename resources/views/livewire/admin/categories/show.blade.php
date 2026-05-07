<section class="w-full">
    <x-admin.layout :heading="__('Category Details')" :subheading="__('Read-only category view')">
        <div class="max-w-2xl rounded-lg border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">{{ $category->name }}</flux:heading>
            <flux:text class="mt-1">{{ __('Slug') }}: {{ $category->slug }}</flux:text>
            <flux:text class="mt-1">
                {{ __('Status') }}:
                {{ $category->trashed() ? __('Trashed') : ($category->is_active ? __('Active') : __('Inactive')) }}
            </flux:text>
            <flux:text class="mt-3">{{ $category->description ?: __('No description provided.') }}</flux:text>

            <div class="mt-5 flex gap-2">
                <flux:button variant="subtle" :href="route('admin.categories.index')" wire:navigate>{{ __('Back') }}</flux:button>
                @if (! $category->trashed())
                    <flux:button variant="primary" :href="route('admin.categories.edit', $category)" wire:navigate>{{ __('Edit') }}</flux:button>
                @endif
            </div>
        </div>
    </x-admin.layout>
</section>
