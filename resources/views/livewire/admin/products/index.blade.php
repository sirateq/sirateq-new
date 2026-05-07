<section class="w-full">
    <x-admin.layout :heading="__('Products')" :subheading="__('Manage catalog products and visibility')" icon="cube">
        <x-slot name="actions">
            <div class="flex flex-wrap items-center gap-2">
                <flux:button variant="outline" icon="arrow-down-tray" :href="route('admin.exports.products', [
                    'q' => $this->search,
                    'status' => $this->status,
                    'sort_by' => $this->sortBy,
                    'sort_direction' => $this->sortDirection,
                ])">
                    {{ __('Export Excel') }}
                </flux:button>
                <flux:button variant="outline" icon="document-arrow-down" :href="route('admin.products.import.template')">
                    {{ __('Import template') }}
                </flux:button>
                <form
                    action="{{ route('admin.products.import') }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="inline-flex"
                    x-data
                    @change="if ($refs.productImport.files?.length) $el.submit()"
                >
                    @csrf
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" class="sr-only" x-ref="productImport" />
                    <flux:button type="button" variant="outline" icon="arrow-up-tray" @click="$refs.productImport.click()">
                        {{ __('Import') }}
                    </flux:button>
                </form>
                <flux:button variant="primary" icon="plus" :href="route('admin.products.create')" wire:navigate>
                    {{ __('Add Product') }}
                </flux:button>
            </div>
        </x-slot>

        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            @if (session('import_status'))
                <flux:callout variant="success" icon="check-circle" class="mx-4 mt-4">
                    {{ session('import_status') }}
                </flux:callout>
            @endif
            @if (filled(session('import_errors')))
                <flux:callout variant="warning" class="mx-4 mt-4">
                    <ul class="list-disc ps-4 text-sm">
                        @foreach (session('import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                </flux:callout>
            @endif
            <div class="flex flex-col gap-3 border-b border-zinc-200/70 p-4 dark:border-zinc-700/60 sm:flex-row sm:items-center sm:justify-between">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name or SKU')" class="sm:max-w-xs" />
                <flux:select wire:model.live="status" class="sm:max-w-xs">
                    <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
                    <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
                    <flux:select.option value="inactive">{{ __('Inactive') }}</flux:select.option>
                </flux:select>
            </div>

            <flux:table :paginate="$this->products">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('Category') }}</flux:table.column>
                    <flux:table.column>{{ __('Variants') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('From') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->products as $product)
                        @php($mainUrl = $product->main_image_url)
                        @php($minPrice = $product->variants->min('price'))
                        <flux:table.row :key="$product->id">
                            <flux:table.cell>
                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    wire:navigate
                                    aria-label="{{ __('Edit :product', ['product' => $product->name]) }}"
                                    class="group flex min-w-0 items-center gap-3 rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900"
                                >
                                    @if ($mainUrl)
                                        <img src="{{ $mainUrl }}" alt="" class="size-11 shrink-0 rounded-lg object-cover ring-1 ring-zinc-200 transition group-hover:ring-zinc-400 dark:ring-zinc-700 dark:group-hover:ring-zinc-500">
                                    @else
                                        <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-zinc-100 ring-1 ring-zinc-200 transition group-hover:ring-zinc-400 dark:bg-zinc-800 dark:ring-zinc-700 dark:group-hover:ring-zinc-500">
                                            <flux:icon name="photo" class="size-4 text-zinc-400" />
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <flux:heading class="!text-sm transition group-hover:text-zinc-900 group-hover:underline dark:group-hover:text-white">{{ $product->name }}</flux:heading>
                                        <flux:text size="sm" class="truncate text-zinc-500">{{ $product->descriptionPlainExcerpt(50) ?: __('No description') }}</flux:text>
                                    </div>
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $product->category->name }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $product->variants->count() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell variant="strong" align="end">
                                {{ $minPrice ? 'GH₵'.number_format((float) $minPrice, 2) : '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$product->is_active ? 'green' : 'zinc'" :icon="$product->is_active ? 'check-circle' : 'minus-circle'" inset="top bottom">
                                    {{ $product->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:tooltip :content="__('Edit')">
                                        <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('admin.products.edit', $product)" wire:navigate />
                                    </flux:tooltip>
                                    <flux:tooltip :content="$product->is_active ? __('Disable') : __('Enable')">
                                        <flux:button variant="ghost" size="sm" :icon="$product->is_active ? 'eye-slash' : 'eye'" wire:click="toggleStatus({{ $product->id }})" />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Delete')">
                                        <flux:button
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                            wire:click="destroy({{ $product->id }})"
                                            wire:confirm="{{ __('Delete this product and all its variants and images? This cannot be undone.') }}" />
                                    </flux:tooltip>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="cube" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No products yet') }}</flux:heading>
                                    <flux:text>{{ __('Add your first product to get started.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
