@props([
    'heading' => '',
    'subheading' => '',
    'icon' => null,
])

<div class="w-full">
    <div class="mb-6 flex flex-col gap-4 border-b border-zinc-200/70 pb-5 dark:border-zinc-700/60 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-start gap-3">
            @if ($icon)
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-900">
                    <flux:icon :name="$icon" class="size-5" />
                </div>
            @endif
            <div>
                <flux:heading size="xl" class="!leading-tight">{{ $heading }}</flux:heading>
                @if ($subheading)
                    <flux:subheading class="mt-1">{{ $subheading }}</flux:subheading>
                @endif
            </div>
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>

    <div class="w-full">
        {{ $slot }}
    </div>
</div>
