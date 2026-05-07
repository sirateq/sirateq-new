<div>
    <x-breadcrum-shop
        image="{{ asset('assets/images/server-woman.png') }}"
        height="160px"
        title="{{ __('Shop') }}"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('home')],
            ['label' => __('Shop')],
        ]"
    />

    <section class="relative overflow-hidden" style="padding-top: 28px; padding-bottom: 88px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 35%, #eef2ff 100%);">
        <div class="pointer-events-none absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(circle at 20% 20%, rgba(6,17,83,0.06) 0%, transparent 45%), radial-gradient(circle at 80% 60%, rgba(16,83,243,0.07) 0%, transparent 40%);"></div>

        <div class="container relative">
            {{-- Toolbar card --}}
            <div class="rounded-2xl border border-white/80 bg-white/90 p-4 shadow-lg shadow-zinc-900/5 backdrop-blur-md sm:p-5">
                <div class="flex flex-col gap-4">
                    {{-- Search --}}
                    <div class="flex w-full min-w-0 flex-col gap-2 sm:flex-row sm:items-stretch">
                        <div class="flex min-w-0 flex-1 items-stretch gap-2 rounded-xl border bg-white p-1.5 shadow-sm"
                             style="border-color: #e5e7eb;">
                            <label class="sr-only" for="shop-search">{{ __('Search products') }}</label>
                            <div class="relative flex flex-1 items-center">
                                {{-- <span class="pointer-events-none absolute left-3 text-zinc-400">
                                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                </span> --}}
                                <input
                                    id="shop-search"
                                    type="search"
                                    wire:model.live.debounce.350ms="search"
                                    placeholder="{{ __('Search name, SKU, variant…') }}"
                                    autocomplete="off"
                                    class="h-11 w-full rounded-lg border-0 bg-transparent pl-10 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#1053f3]/30"
                                />
                            </div>
                            @if (filled(trim($search)))
                                <button type="button" wire:click="$set('search', '')"
                                        class="shrink-0 rounded-lg px-3 text-sm font-semibold text-[#061153] transition hover:bg-zinc-100 sm:px-4">
                                    {{ __('Clear') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex min-w-0 flex-1 flex-wrap items-center gap-x-4 gap-y-2">
                            <p class="m-0 text-sm text-zinc-600">
                                <span class="font-semibold tabular-nums" style="color: #061153;">{{ $this->products->count() }}</span>
                                @if (filled(trim($search)))
                                    {{ __('results for') }}
                                    <span class="font-semibold" style="color: #061153;">“{{ \Illuminate\Support\Str::limit(trim($search), 40) }}”</span>
                                @else
                                    {{ __('products') }}
                                @endif
                            </p>
                            <div class="flex flex-wrap items-center gap-3 text-sm font-semibold" style="color: #061153;">
                                <a href="{{ route('shop.cart') }}" wire:navigate
                                   class="inline-flex items-center gap-2 rounded-lg px-1 py-0.5 transition hover:bg-zinc-100 hover:text-[#1053f3]">
                                    <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                                    {{ __('Cart') }}
                                </a>
                                <span class="hidden h-4 w-px bg-zinc-200 sm:inline" aria-hidden="true"></span>
                                <a href="{{ route('shop.orders.track') }}" wire:navigate
                                   class="inline-flex items-center gap-2 rounded-lg px-1 py-0.5 transition hover:bg-zinc-100 hover:text-[#1053f3]">
                                    <i class="fa-solid fa-truck" aria-hidden="true"></i>
                                    {{ __('Track order') }}
                                </a>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <div class="inline-flex overflow-hidden rounded-xl border bg-white" style="border-color: #e5e7eb;">
                            <button type="button" wire:click="setView('grid')"
                                    class="flex h-10 w-10 items-center justify-center transition sm:h-11 sm:w-11"
                                    style="{{ $view === 'grid' ? 'background:#061153; color:#fff;' : 'background:#fff; color:#6b7280;' }}"
                                    aria-label="{{ __('Grid view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="setView('list')"
                                    class="flex h-10 w-10 items-center justify-center transition sm:h-11 sm:w-11"
                                    style="{{ $view === 'list' ? 'background:#061153; color:#fff;' : 'background:#fff; color:#6b7280;' }}"
                                    aria-label="{{ __('List view') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        <div class="relative">
                            <select wire:model.live="sort"
                                    class="h-10 appearance-none rounded-xl border bg-white pl-4 pr-10 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#1053f3]/30 sm:h-11"
                                    style="color: #061153; border-color: #e5e7eb;">
                                <option value="default">{{ __('Default sorting') }}</option>
                                <option value="newest">{{ __('Newest') }}</option>
                                <option value="price_asc">{{ __('Price: low to high') }}</option>
                                <option value="price_desc">{{ __('Price: high to low') }}</option>
                                <option value="name">{{ __('Name: A → Z') }}</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center" style="color: #061153;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><polyline points="6 9 12 15 18 9"/></svg>
                            </span>
                        </div>

                        <button type="button" wire:click="toggleFilter"
                                class="inline-flex h-10 items-center gap-2 rounded-xl px-4 text-sm font-semibold text-white shadow-md transition hover:brightness-110 sm:h-11 sm:px-5"
                                style="background: linear-gradient(135deg, #061153, #1053f3); box-shadow: 0 4px 14px rgba(6, 17, 83, 0.25);">
                            <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                            {{ __('Filters') }}
                        </button>
                    </div>
                </div>
            </div>
            </div>

            {{-- Filter panel --}}
            @if ($filterOpen)
                <div class="mt-5 rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-md">
                    <div class="flex flex-wrap items-start gap-6">
                        <div class="min-w-[200px] flex-1">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Category') }}</p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" wire:click="setCategory(null)"
                                        class="rounded-full border px-3 py-1.5 text-sm font-medium transition"
                                        style="{{ $categoryId === null ? 'border-color:#061153; background:#061153; color:#fff;' : 'border-color:#e5e7eb; background:#fff; color:#061153;' }}">
                                    {{ __('All') }}
                                </button>
                                @foreach ($this->categories as $cat)
                                    <button type="button" wire:click="setCategory({{ $cat->id }})"
                                            class="rounded-full border px-3 py-1.5 text-sm font-medium transition"
                                            style="{{ $categoryId === $cat->id ? 'border-color:#061153; background:#061153; color:#fff;' : 'border-color:#e5e7eb; background:#fff; color:#061153;' }}">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="min-w-[260px] flex-1">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Price range') }} (GH₵)</p>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="{{ __('Min') }}"
                                       class="h-10 w-full rounded-xl border px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1053f3]/25 sm:h-11"
                                       style="border-color: #e5e7eb; color: #061153;">
                                <span class="text-zinc-400">—</span>
                                <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="{{ __('Max') }}"
                                       class="h-10 w-full rounded-xl border px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1053f3]/25 sm:h-11"
                                       style="border-color: #e5e7eb; color: #061153;">
                            </div>
                        </div>

                        <div class="ml-auto flex flex-wrap items-center gap-2 self-end">
                            <button type="button" wire:click="clearFilters"
                                    class="rounded-xl px-4 py-2 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                                {{ __('Reset all') }}
                            </button>
                            <button type="button" wire:click="toggleFilter"
                                    class="rounded-xl px-4 py-2 text-sm font-semibold text-white"
                                    style="background: #061153;">
                                {{ __('Done') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Products --}}
            @if ($this->products->isEmpty())
                <div class="mt-14 flex flex-col items-center gap-4 rounded-3xl border border-dashed border-zinc-300/80 bg-white/80 px-6 py-16 text-center shadow-sm">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400">
                        <i class="fa-solid fa-box-open text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-lg font-semibold" style="color: #061153;">{{ __('No products match your search or filters') }}</p>
                    <p class="m-0 max-w-md text-sm text-zinc-500">{{ __('Try another keyword, clear the search, or reset filters to see everything again.') }}</p>
                    <button type="button" wire:click="clearFilters"
                            class="mt-1 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition hover:brightness-110"
                            style="background: linear-gradient(135deg, #061153, #1053f3);">
                        {{ __('Clear search & filters') }}
                    </button>
                </div>
            @elseif ($view === 'grid')
                <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($this->products as $product)
                        @php($mainUrl = $product->main_image_url)
                        @php($isNew = $product->created_at?->gt(now()->subDays(14)))
                        @php($outOfStock = $product->isOutOfStock())
                        <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                           class="group relative flex flex-col overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-md shadow-zinc-900/5 transition duration-300 hover:-translate-y-1 hover:border-[#1053f3]/35 hover:shadow-xl hover:shadow-[#061153]/10"
                           wire:key="product-{{ $product->id }}">
                            <div class="relative aspect-square overflow-hidden bg-zinc-100 {{ $outOfStock ? 'opacity-85' : '' }}">
                                @if ($mainUrl)
                                    <img src="{{ $mainUrl }}" alt="{{ $product->name }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-zinc-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-12 w-12">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/25 to-transparent opacity-0 transition group-hover:opacity-100"></div>
                                <div class="absolute left-3 top-3 z-[1] flex flex-wrap gap-2">
                                    @if ($isNew)
                                        <span class="rounded-lg bg-amber-400 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-zinc-900 shadow-sm">
                                            {{ __('New') }}
                                        </span>
                                    @endif
                                    @if ($outOfStock)
                                        <span class="rounded-lg bg-red-600 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                                            {{ __('Out of stock') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4 text-center sm:p-5">
                                <p class="text-xl font-bold tabular-nums sm:text-2xl" style="color: #061153;">
                                    {{ $product->storefrontVariantPriceLabel() }}
                                </p>
                                <p class="mt-2 line-clamp-2 text-sm font-semibold leading-snug" style="color: #0f172a;">
                                    {{ $product->name }}
                                </p>
                                <p class="mt-1 text-xs font-medium text-zinc-500">
                                    {{ $product->category->name }}
                                </p>
                                <span class="mt-4 inline-flex items-center justify-center gap-1 text-xs font-semibold text-[#1053f3] opacity-0 transition group-hover:opacity-100">
                                    {{ __('View') }}
                                    <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-10 space-y-4">
                    @foreach ($this->products as $product)
                        @php($mainUrl = $product->main_image_url)
                        @php($isNew = $product->created_at?->gt(now()->subDays(14)))
                        @php($outOfStock = $product->isOutOfStock())
                        <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                           class="group flex items-center gap-5 overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-md transition hover:border-[#1053f3]/30 hover:shadow-lg sm:p-5"
                           wire:key="product-list-{{ $product->id }}">
                            <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-zinc-100 sm:h-32 sm:w-32 {{ $outOfStock ? 'opacity-85' : '' }}">
                                @if ($mainUrl)
                                    <img src="{{ $mainUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @endif
                                <div class="absolute left-2 top-2 z-[1] flex flex-col gap-1.5">
                                    @if ($isNew)
                                        <span class="rounded-md bg-amber-400 px-1.5 py-0.5 text-[10px] font-bold text-zinc-900">
                                            {{ __('New') }}
                                        </span>
                                    @endif
                                    @if ($outOfStock)
                                        <span class="rounded-md bg-red-600 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white">
                                            {{ __('Out of stock') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-1 items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-base font-bold" style="color: #061153;">{{ $product->name }}</p>
                                    <p class="mt-1 text-sm text-zinc-500">{{ $product->category->name }}</p>
                                    <p class="mt-2 line-clamp-2 text-sm text-zinc-600">
                                        {{ $product->descriptionPlainExcerpt(220) ?: __('No description yet.') }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-xl font-bold tabular-nums sm:text-2xl" style="color: #061153;">
                                        {{ $product->storefrontVariantPriceLabel() }}
                                    </p>
                                    <p class="mt-1 text-xs text-zinc-500">
                                        {{ $product->variants->count() }} {{ \Illuminate\Support\Str::plural(__('option'), $product->variants->count()) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
