<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="bg-zinc-200 dark:bg-zinc-900">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
