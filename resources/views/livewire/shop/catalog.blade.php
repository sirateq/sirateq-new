<section class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Shop Catalog') }}</flux:heading>
        <flux:subheading>{{ __('Browse products and add them to your cart.') }}</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($products as $product)
            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700" wire:key="product-{{ $product->id }}">
                <flux:heading>{{ $product->name }}</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $product->category->name }}</flux:text>
                <flux:text class="mt-2">{{ $product->description ?: __('No description yet.') }}</flux:text>
                <flux:button class="mt-4" variant="primary" :href="route('shop.products.show', $product->slug)" wire:navigate>
                    {{ __('View Product') }}
                </flux:button>
            </div>
        @empty
            <flux:callout icon="information-circle" variant="warning">
                {{ __('No active products found.') }}
            </flux:callout>
        @endforelse
    </div>
</section>
