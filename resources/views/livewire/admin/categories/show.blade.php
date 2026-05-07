<section class="w-full">
    <x-admin.layout :heading="$category->name" :subheading="__('Read-only category view')" icon="squares-2x2">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.categories.index')" wire:navigate>
                {{ __('Back') }}
            </flux:button>
            @if (! $category->trashed())
                <flux:button variant="primary" icon="pencil-square" :href="route('admin.categories.edit', $category)" wire:navigate>
                    {{ __('Edit') }}
                </flux:button>
            @endif
        </x-slot>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900 lg:col-span-2">
                <header class="mb-4 flex items-center justify-between gap-2">
                    <flux:heading size="lg">{{ __('Description') }}</flux:heading>
                    @if ($category->trashed())
                        <flux:badge size="sm" color="red" icon="trash" inset="top bottom">{{ __('Trashed') }}</flux:badge>
                    @else
                        <flux:badge size="sm" :color="$category->is_active ? 'green' : 'zinc'" :icon="$category->is_active ? 'check-circle' : 'minus-circle'" inset="top bottom">
                            {{ $category->is_active ? __('Active') : __('Inactive') }}
                        </flux:badge>
                    @endif
                </header>
                <flux:separator class="mb-4" />
                <flux:text>{{ $category->description ?: __('No description provided.') }}</flux:text>
            </div>

            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">{{ __('Metadata') }}</flux:heading>
                <flux:separator class="mb-4" />
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-zinc-500">{{ __('Slug') }}</dt>
                        <dd class="font-mono text-xs">{{ $category->slug }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-zinc-500">{{ __('Created') }}</dt>
                        <dd>{{ $category->created_at?->diffForHumans() }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-zinc-500">{{ __('Updated') }}</dt>
                        <dd>{{ $category->updated_at?->diffForHumans() }}</dd>
                    </div>
                    @if ($category->trashed())
                        <div class="flex items-center justify-between">
                            <dt class="text-zinc-500">{{ __('Trashed') }}</dt>
                            <dd>{{ $category->deleted_at?->diffForHumans() }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </x-admin.layout>
</section>
