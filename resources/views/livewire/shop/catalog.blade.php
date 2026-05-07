<div>
    <x-breadcrum-shop image="{{ asset('assets/images/server-woman.png') }}" />

    <section class="bg-light" style="padding-top: 60px; padding-bottom: 80px;">
        <div class="container">
    {{-- Top toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-zinc-700 dark:text-zinc-300">
            {{ __('We found') }}
            <span class="font-semibold" style="color: #061153;">{{ $this->products->count() }}</span>
            {{ __('products available for you') }}
        </p>

        <div class="flex flex-wrap items-center gap-3">
            {{-- View toggle --}}
            <div class="inline-flex overflow-hidden rounded-lg border bg-white" style="border-color: #e5e7eb;">
                <button type="button" wire:click="setView('grid')"
                        class="flex h-10 w-10 items-center justify-center transition"
                        style="{{ $view === 'grid' ? 'background:#061153; color:#fff;' : 'background:#fff; color:#6b7280;' }}"
                        aria-label="{{ __('Grid view') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </button>
                <button type="button" wire:click="setView('list')"
                        class="flex h-10 w-10 items-center justify-center transition"
                        style="{{ $view === 'list' ? 'background:#061153; color:#fff;' : 'background:#fff; color:#6b7280;' }}"
                        aria-label="{{ __('List view') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Sort --}}
            <div class="relative">
                <select wire:model.live="sort"
                        class="h-10 appearance-none rounded-lg border bg-white pl-4 pr-10 text-sm focus:outline-none"
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

            {{-- Filter button --}}
            <button type="button" wire:click="toggleFilter"
                    class="inline-flex h-10 items-center gap-2 rounded-lg px-5 text-sm font-medium text-white shadow-sm transition"
                    style="background: #061153;"
                    onmouseover="this.style.background='#1053f3'"
                    onmouseout="this.style.background='#061153'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <line x1="4" y1="6" x2="14" y2="6"/><line x1="18" y1="6" x2="20" y2="6"/>
                    <circle cx="16" cy="6" r="2"/>
                    <line x1="4" y1="12" x2="8" y2="12"/><line x1="12" y1="12" x2="20" y2="12"/>
                    <circle cx="10" cy="12" r="2"/>
                    <line x1="4" y1="18" x2="16" y2="18"/><line x1="20" y1="18" x2="20" y2="18"/>
                    <circle cx="18" cy="18" r="2"/>
                </svg>
                {{ __('Filter') }}
            </button>
        </div>
    </div>

    {{-- Filter panel --}}
    @if ($filterOpen)
        <div class="mt-6 rounded-2xl border bg-white p-5 shadow-sm" style="border-color: #e5e7eb;">
            <div class="flex flex-wrap items-start gap-6">
                <div class="min-w-[200px] flex-1">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide" style="color: #6b7280;">{{ __('Category') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="setCategory(null)"
                                class="rounded-full border px-3 py-1 text-sm transition"
                                style="{{ $categoryId === null ? 'border-color:#061153; background:#061153; color:#fff;' : 'border-color:#e5e7eb; background:#fff; color:#061153;' }}">
                            {{ __('All') }}
                        </button>
                        @foreach ($this->categories as $cat)
                            <button type="button" wire:click="setCategory({{ $cat->id }})"
                                    class="rounded-full border px-3 py-1 text-sm transition"
                                    style="{{ $categoryId === $cat->id ? 'border-color:#061153; background:#061153; color:#fff;' : 'border-color:#e5e7eb; background:#fff; color:#061153;' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="min-w-[260px] flex-1">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide" style="color: #6b7280;">{{ __('Price range') }}</p>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="{{ __('Min') }}"
                               class="h-10 w-full rounded-lg border px-3 text-sm focus:outline-none"
                               style="border-color: #e5e7eb; color: #061153;">
                        <span style="color: #9ca3af;">—</span>
                        <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="{{ __('Max') }}"
                               class="h-10 w-full rounded-lg border px-3 text-sm focus:outline-none"
                               style="border-color: #e5e7eb; color: #061153;">
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-2 self-end">
                    <button type="button" wire:click="clearFilters"
                            class="rounded-lg px-4 py-2 text-sm font-medium"
                            style="color: #061153; background: transparent;"
                            onmouseover="this.style.background='#f4f4f5'"
                            onmouseout="this.style.background='transparent'">
                        {{ __('Reset') }}
                    </button>
                    <button type="button" wire:click="toggleFilter"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-white"
                            style="background: #061153;"
                            onmouseover="this.style.background='#1053f3'"
                            onmouseout="this.style.background='#061153'">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Products --}}
    @if ($this->products->isEmpty())
        <div class="mt-12 flex flex-col items-center gap-3 rounded-2xl border border-dashed border-zinc-300 bg-white py-16 text-center dark:border-zinc-700 dark:bg-zinc-900">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-zinc-400">
                <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            <p class="font-semibold" style="color: #061153;">{{ __('No products match your filters') }}</p>
            <button wire:click="clearFilters" class="text-sm underline" style="color: #1053f3;">
                {{ __('Clear filters') }}
            </button>
        </div>
    @elseif ($view === 'grid')
        <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($this->products as $product)
                @php($mainUrl = $product->main_image_url)
                @php($minPrice = (float) ($product->variants->min('price') ?? 0))
                @php($isNew = $product->created_at?->gt(now()->subDays(14)))
                <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                   class="group flex flex-col"
                   wire:key="product-{{ $product->id }}">
                    <div class="relative aspect-square overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                        @if ($mainUrl)
                            <img src="{{ $mainUrl }}" alt="{{ $product->name }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-zinc-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-10 w-10">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        @endif

                        @if ($isNew)
                            <span class="absolute left-3 top-3 rounded-md bg-amber-400 px-2 py-1 text-[11px] font-semibold tracking-wide text-zinc-900">
                                {{ __('New') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-col items-center text-center">
                        <p class="text-sm font-semibold" style="color: #061153;">
                            GH₵{{ number_format($minPrice, 2) }}
                        </p>
                        <p class="mt-1 text-sm" style="color: #061153;">
                            {{ $product->name }}
                        </p>
                        <p class="mt-1 text-xs" style="color: #6b7280;">
                            {{ $product->category->name }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($this->products as $product)
                @php($mainUrl = $product->main_image_url)
                @php($minPrice = (float) ($product->variants->min('price') ?? 0))
                @php($isNew = $product->created_at?->gt(now()->subDays(14)))
                <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                   class="group flex items-center gap-5 rounded-2xl border bg-white p-4 transition hover:shadow-md"
                   style="border-color: #e5e7eb;"
                   wire:key="product-list-{{ $product->id }}">
                    <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800 sm:h-32 sm:w-32">
                        @if ($mainUrl)
                            <img src="{{ $mainUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                        @endif
                        @if ($isNew)
                            <span class="absolute left-2 top-2 rounded bg-amber-400 px-1.5 py-0.5 text-[10px] font-semibold text-zinc-900">
                                {{ __('New') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-1 items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-base font-semibold" style="color: #061153;">{{ $product->name }}</p>
                            <p class="mt-1 text-sm" style="color: #6b7280;">{{ $product->category->name }}</p>
                            <p class="mt-2 line-clamp-2 text-sm" style="color: #4b5563;">
                                {{ $product->description ?: __('No description yet.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-semibold" style="color: #061153;">
                                GH₵{{ number_format($minPrice, 2) }}
                            </p>
                            <p class="mt-1 text-xs" style="color: #6b7280;">
                                {{ $product->variants->count() }} {{ Str::plural(__('option'), $product->variants->count()) }}
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

