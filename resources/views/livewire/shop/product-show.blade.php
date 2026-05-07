<section class="space-y-6">
    <div>
        <flux:button variant="ghost" :href="route('shop.index')" wire:navigate>{{ __('Back to catalog') }}</flux:button>
    </div>

    <div class="rounded-lg border border-zinc-200 p-6 dark:border-zinc-700">
        <flux:heading size="xl">{{ $product->name }}</flux:heading>
        <flux:subheading>{{ $product->category->name }}</flux:subheading>
        <flux:text class="mt-3">{{ $product->description ?: __('No description yet.') }}</flux:text>

        <form wire:submit="addToCart" class="mt-6 space-y-4">
            <flux:select wire:model="selectedVariant" :label="__('Variant')">
                @foreach ($product->variants as $variant)
                    <flux:select.option value="{{ $variant->id }}">
                        {{ $variant->name }} - ${{ number_format((float) $variant->price, 2) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="quantity" type="number" min="1" :label="__('Quantity')" />

            <flux:button variant="primary" type="submit">{{ __('Add to cart') }}</flux:button>
        </form>
    </div>
</section>
