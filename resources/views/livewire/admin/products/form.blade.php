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
                    <flux:textarea wire:model="description" :label="__('Description')" :placeholder="__('Markdown or HTML. Leave blank if not needed.')" class="md:col-span-2 min-h-[min(28rem,55vh)]" rows="16" />
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

            {{-- Product options (dimensions) --}}
            <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <header class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <flux:icon name="rectangle-stack" class="size-5 text-zinc-500" />
                        <flux:heading size="lg">{{ __('Product options') }}</flux:heading>
                    </div>
                    <flux:button type="button" variant="subtle" size="sm" icon="plus" wire:click="addOptionGroup">
                        {{ __('Add option') }}
                    </flux:button>
                </header>
                <flux:subheading class="mb-4">{{ __('Define dimensions such as Color, Size, or Gender. Each sellable variant picks one value per option.') }}</flux:subheading>
                <flux:separator class="mb-5" />

                <div class="space-y-6">
                    @foreach ($optionGroups as $gi => $group)
                        <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/50 p-4 dark:border-zinc-700/60 dark:bg-zinc-800/30"
                             wire:key="opt-group-{{ $gi }}">
                            <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                                <div class="grid flex-1 gap-3 sm:grid-cols-3">
                                    <flux:input wire:model.blur="optionGroups.{{ $gi }}.name" :label="__('Option name')" :placeholder="__('e.g. Color, Size, Gender')" />
                                    <flux:select wire:model.live="optionGroups.{{ $gi }}.display_type" :label="__('Display style')">
                                        <flux:select.option value="text">{{ __('Text / chip') }}</flux:select.option>
                                        <flux:select.option value="swatch_color">{{ __('Color swatch') }}</flux:select.option>
                                        <flux:select.option value="swatch_image">{{ __('Image swatch') }}</flux:select.option>
                                    </flux:select>
                                    @if (count($optionGroups) > 1)
                                        <div class="flex items-end">
                                            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeOptionGroup({{ $gi }})">
                                                {{ __('Remove option') }}
                                            </flux:button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2">
                                @foreach ($group['values'] as $vi => $val)
                                    <div class="flex flex-wrap items-end gap-2 rounded-lg border border-zinc-200/70 bg-white p-3 dark:border-zinc-700/60 dark:bg-zinc-900"
                                         wire:key="opt-val-{{ $gi }}-{{ $vi }}">
                                        <flux:input wire:model.blur="optionGroups.{{ $gi }}.values.{{ $vi }}.label" :label="__('Value label')" class="min-w-[140px] flex-1" :placeholder="__('e.g. Red, Large')" />
                                        @if (($group['display_type'] ?? 'text') === 'swatch_color')
                                            <flux:field>
                                                <flux:label>{{ __('Swatch color') }}</flux:label>
                                                <input
                                                    type="color"
                                                    wire:model.live="optionGroups.{{ $gi }}.values.{{ $vi }}.hex_color"
                                                    class="h-10 w-14 cursor-pointer rounded-md border border-zinc-300 bg-white p-1 shadow-sm dark:border-zinc-600 dark:bg-zinc-900"
                                                />
                                                <flux:error name="optionGroups.{{ $gi }}.values.{{ $vi }}.hex_color" />
                                            </flux:field>
                                        @endif
                                        @if (($group['display_type'] ?? 'text') === 'swatch_image')
                                            <flux:select wire:model="optionGroups.{{ $gi }}.values.{{ $vi }}.product_image_id" :label="__('Swatch image')" class="min-w-[200px] flex-1">
                                                <flux:select.option value="">{{ __('None') }}</flux:select.option>
                                                @foreach ($swatchImageChoices as $choice)
                                                    <flux:select.option value="{{ $choice['id'] }}">{{ $choice['label'] }}</flux:select.option>
                                                @endforeach
                                            </flux:select>
                                        @endif
                                        @if (count($group['values']) > 1)
                                            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeOptionValue({{ $gi }}, {{ $vi }})" />
                                        @endif
                                    </div>
                                @endforeach
                                <flux:button type="button" variant="ghost" size="sm" icon="plus" wire:click="addOptionValue({{ $gi }})">
                                    {{ __('Add value') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Variants (SKUs / price / stock) --}}
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
                <flux:subheading class="mb-4">{{ __('Each row is one sellable SKU: pick one value per option, then set price and stock.') }}</flux:subheading>
                <flux:separator class="mb-5" />

                @error('variants')
                    <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4">{{ $message }}</flux:callout>
                @enderror

                <div class="space-y-3">
                    @foreach ($variants as $idx => $variant)
                        <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/60 p-4 dark:border-zinc-700/60 dark:bg-zinc-800/40"
                             wire:key="variant-{{ $variant['key'] }}">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                                @foreach ($optionGroups as $gi => $g)
                                    <div class="md:col-span-3">
                                        <flux:select wire:model.live="variants.{{ $idx }}.selected_value_indexes.{{ $gi }}" :label="$g['name']" :placeholder="$g['name']">
                                            @foreach ($g['values'] as $vi => $optVal)
                                                <flux:select.option value="{{ $vi }}">{{ $optVal['label'] }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                @endforeach
                                <div class="md:col-span-3">
                                    <flux:input
                                        wire:model="variants.{{ $idx }}.sku"
                                        :label="__('SKU')"
                                        :placeholder="__('Optional — auto-generated if empty')" />
                                    <flux:text size="sm" class="mt-1 text-zinc-500">{{ __('Leave blank for an automatic SKU (e.g. SQ-…).') }}</flux:text>
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
