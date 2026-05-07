<section class="w-full">
    <x-admin.layout
        :heading="$product ? __('Edit Product') : __('New Product')"
        :subheading="__('Manage product details, images, variants and inventory')"
        icon="cube">
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.products.index')" wire:navigate>
                {{ __('Back') }}
            </flux:button>
        </x-slot>

        <form wire:submit="save" class="space-y-6">
            {{-- Basic information --}}
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <header class="mb-4 flex items-center gap-2">
                    <flux:icon name="information-circle" class="size-5 text-zinc-500" />
                    <flux:heading size="lg">{{ __('Basic information') }}</flux:heading>
                </header>
                <flux:separator class="mb-5" />

                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="name" :label="__('Product name')" :placeholder="__('e.g. Linen Shirt')" required />
                    <flux:select wire:model="category_id" :label="__('Category')" required>
                        <flux:select.option value="">{{ __('Select category') }}</flux:select.option>
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:textarea wire:model="description" :label="__('Description')" :placeholder="__('Short summary of the product')" class="md:col-span-2" rows="3" />
                    <div class="rounded-lg border border-zinc-200/70 bg-zinc-50 p-4 dark:border-zinc-700/60 dark:bg-zinc-800/40 md:col-span-2">
                        <flux:switch wire:model="is_active" :label="__('Visible on storefront')" :description="__('Disabled products stay hidden from shoppers')" />
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <header class="mb-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <flux:icon name="photo" class="size-5 text-zinc-500" />
                        <flux:heading size="lg">{{ __('Images') }}</flux:heading>
                    </div>
                    <flux:subheading>{{ __('Click an image to mark it as the main one') }}</flux:subheading>
                </header>
                <flux:separator class="mb-5" />

                <div class="rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800/40">
                    <div class="flex flex-col items-center gap-3 text-center">
                        <flux:icon name="cloud-arrow-up" class="size-8 text-zinc-400" />
                        <div>
                            <flux:heading class="!text-sm">{{ __('Add product images') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500">{{ __('PNG, JPG or WEBP up to 5 MB each') }}</flux:text>
                        </div>
                        <flux:input type="file" wire:model="newImages" multiple accept="image/*" />
                        <div wire:loading wire:target="newImages" class="flex items-center gap-2 text-amber-600">
                            <flux:icon name="arrow-path" class="size-4 animate-spin" />
                            <flux:text size="sm">{{ __('Uploading…') }}</flux:text>
                        </div>
                    </div>
                </div>

                @error('newImages.*')
                    <flux:callout variant="danger" icon="exclamation-triangle" class="mt-3">{{ $message }}</flux:callout>
                @enderror

                @php($hasImages = count($existingImages) + count($newImages) > 0)
                @if ($hasImages)
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($existingImages as $image)
                            @php($isPrimary = $primarySelector === 'existing:'.$image['id'])
                            <div class="group relative overflow-hidden rounded-xl border-2 transition {{ $isPrimary ? 'border-zinc-900 ring-2 ring-zinc-900/20 dark:border-white dark:ring-white/20' : 'border-zinc-200 dark:border-zinc-700' }}"
                                 wire:key="existing-image-{{ $image['id'] }}">
                                <button type="button" wire:click="setPrimaryExisting({{ $image['id'] }})" class="block w-full">
                                    <img src="{{ $image['url'] }}" alt="" class="aspect-square w-full object-cover">
                                </button>
                                @if ($isPrimary)
                                    <div class="absolute left-2 top-2">
                                        <flux:badge size="sm" color="zinc" icon="star" inset="top bottom">{{ __('Main') }}</flux:badge>
                                    </div>
                                @else
                                    <div class="absolute left-2 top-2 opacity-0 transition group-hover:opacity-100">
                                        <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('Set main') }}</flux:badge>
                                    </div>
                                @endif
                                <button type="button" wire:click="removeExistingImage({{ $image['id'] }})"
                                        class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-white/90 text-red-600 shadow opacity-0 transition group-hover:opacity-100 dark:bg-zinc-900/90">
                                    <flux:icon name="x-mark" class="size-4" />
                                </button>
                            </div>
                        @endforeach

                        @foreach ($newImages as $idx => $upload)
                            @php($isPrimary = $primarySelector === 'new:'.$idx)
                            <div class="group relative overflow-hidden rounded-xl border-2 transition {{ $isPrimary ? 'border-zinc-900 ring-2 ring-zinc-900/20 dark:border-white dark:ring-white/20' : 'border-zinc-200 dark:border-zinc-700' }}"
                                 wire:key="new-image-{{ $idx }}">
                                <button type="button" wire:click="setPrimaryNew({{ $idx }})" class="block w-full">
                                    <img src="{{ $upload->temporaryUrl() }}" alt="" class="aspect-square w-full object-cover">
                                </button>
                                <div class="absolute left-2 top-2 flex items-center gap-1">
                                    <flux:badge size="sm" color="blue" icon="sparkles" inset="top bottom">{{ __('New') }}</flux:badge>
                                    @if ($isPrimary)
                                        <flux:badge size="sm" color="zinc" icon="star" inset="top bottom">{{ __('Main') }}</flux:badge>
                                    @endif
                                </div>
                                <button type="button" wire:click="removeNewImage({{ $idx }})"
                                        class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-white/90 text-red-600 shadow opacity-0 transition group-hover:opacity-100 dark:bg-zinc-900/90">
                                    <flux:icon name="x-mark" class="size-4" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:text class="mt-4 text-zinc-500">{{ __('No images yet. Upload at least one to feature this product.') }}</flux:text>
                @endif
            </div>

            {{-- Variants --}}
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <header class="mb-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <flux:icon name="rectangle-stack" class="size-5 text-zinc-500" />
                        <flux:heading size="lg">{{ __('Variants & inventory') }}</flux:heading>
                    </div>
                    <flux:button type="button" variant="subtle" size="sm" icon="plus" wire:click="addVariant">
                        {{ __('Add variant') }}
                    </flux:button>
                </header>
                <flux:subheading class="mb-4">{{ __('Sizes, colors, or options. Each one tracks its own SKU and stock.') }}</flux:subheading>
                <flux:separator class="mb-5" />

                @error('variants')
                    <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4">{{ $message }}</flux:callout>
                @enderror

                <div class="space-y-3">
                    @foreach ($variants as $idx => $variant)
                        <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/60 p-4 dark:border-zinc-700/60 dark:bg-zinc-800/40"
                             wire:key="variant-{{ $variant['key'] }}">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                                <div class="md:col-span-3">
                                    <flux:input wire:model="variants.{{ $idx }}.name" :label="__('Variant')" :placeholder="__('e.g. Medium')" />
                                </div>
                                <div class="md:col-span-3">
                                    <flux:input wire:model="variants.{{ $idx }}.sku" :label="__('SKU')" :placeholder="__('LS-M-001')" />
                                </div>
                                <div class="md:col-span-3">
                                    <flux:input wire:model="variants.{{ $idx }}.price" type="number" step="0.01" min="0" :label="__('Price')" icon="currency-dollar" />
                                </div>
                                <div class="md:col-span-2">
                                    <flux:input wire:model="variants.{{ $idx }}.quantity" type="number" min="0" :label="__('Stock')" icon="archive-box" />
                                </div>
                                <div class="flex items-end justify-end md:col-span-1">
                                    @if (count($variants) > 1)
                                        <flux:tooltip :content="__('Remove')">
                                            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeVariant('{{ $variant['key'] }}')" />
                                        </flux:tooltip>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col-reverse items-stretch justify-end gap-2 sm:flex-row sm:items-center">
                <flux:button variant="ghost" :href="route('admin.products.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ $product ? __('Save changes') : __('Create product') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </x-admin.layout>
</section>
