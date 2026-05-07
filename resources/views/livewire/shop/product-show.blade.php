<div>
    <x-breadcrum-shop
        image="{{ asset('assets/images/server-woman.png') }}"
        title="{{ $product->name }}"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('home')],
            ['label' => __('Shop'), 'url' => route('shop.index')],
            ['label' => $product->name],
        ]"
    />

    @php($selected = $product->variants->firstWhere('id', (int) $selectedVariant) ?? $product->variants->first())
    @php($stock = (int) optional($selected?->inventoryItem)->quantity)
    @php($mainPrice = (float) ($selected?->price ?? 0))

    <section class="bg-light" style="padding-top: 60px; padding-bottom: 80px;">
        <div class="container">
            <div class="row gy-30 align-items-start">
                {{-- Gallery --}}
                <div class="col-lg-6">
                    <div class="row gy-3">
                        {{-- Thumbnails column --}}
                        @if ($product->images->isNotEmpty() && $product->images->count() > 1)
                            <div class="col-3 d-none d-lg-block">
                                <div class="d-flex flex-column" style="gap: 12px;">
                                    @foreach ($product->images as $image)
                                        <button type="button"
                                                wire:click="setActiveImage('{{ $image->url }}')"
                                                wire:key="thumb-{{ $image->id }}"
                                                style="width: 100%; aspect-ratio: 1 / 1; padding: 0; border-radius: 8px; overflow: hidden; background: #fff; border: 2px solid {{ $activeImage === $image->url ? '#061153' : '#e5e7eb' }}; cursor: pointer;">
                                            <img src="{{ $image->url }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Main image --}}
                        <div class="{{ $product->images->count() > 1 ? 'col-lg-9' : 'col-12' }} col-12">
                            <div style="width: 100%; aspect-ratio: 1 / 1; background: #f4f4f5; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                @if ($activeImage)
                                    <img src="{{ $activeImage }}" alt="{{ $product->name }}"
                                         style="max-width: 100%; max-height: 100%; width: 100%; height: 100%; object-fit: contain; display: block;">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: #a1a1aa;">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Mobile thumbnails (horizontal) --}}
                            @if ($product->images->isNotEmpty() && $product->images->count() > 1)
                                <div class="d-flex d-lg-none mt-3" style="gap: 8px; overflow-x: auto;">
                                    @foreach ($product->images as $image)
                                        <button type="button"
                                                wire:click="setActiveImage('{{ $image->url }}')"
                                                wire:key="m-thumb-{{ $image->id }}"
                                                style="flex: 0 0 auto; width: 64px; height: 64px; padding: 0; border-radius: 8px; overflow: hidden; background: #fff; border: 2px solid {{ $activeImage === $image->url ? '#061153' : '#e5e7eb' }}; cursor: pointer;">
                                            <img src="{{ $image->url }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="col-lg-6">
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        {{-- Price --}}
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 24px; font-weight: 700; color: #061153;">GH₵{{ number_format($mainPrice, 2) }}</span>
                            @if ($product->variants->count() > 1)
                                <span style="background: #059669; color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    {{ $product->variants->count() }} {{ __('options') }}
                                </span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h1 style="font-size: 30px; font-weight: 700; line-height: 1.2; color: #061153; margin: 0;">{{ $product->name }}</h1>

                        {{-- Stock indicator --}}
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            @if ($stock > 5)
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 999px; background: #10b981;"></span>
                                <span style="color: #047857;">{{ __('In stock') }}</span>
                            @elseif ($stock > 0)
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 999px; background: #f59e0b;"></span>
                                <span style="color: #b45309;">{{ __('Only :n left', ['n' => $stock]) }}</span>
                            @else
                                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 999px; background: #ef4444;"></span>
                                <span style="color: #b91c1c;">{{ __('Out of stock') }}</span>
                            @endif
                        </div>

                        {{-- Description --}}
                        <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin: 0;">
                            {{ $product->description ?: __('No description yet.') }}
                        </p>

                        {{-- Variant selector --}}
                        @if ($product->variants->count() > 1)
                            <div>
                                <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #061153;">
                                    {{ __('Size:') }}
                                    <span style="font-weight: 400; color: #4b5563;">{{ $selected?->name }}</span>
                                </p>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @foreach ($product->variants as $variant)
                                        @php($isSelected = (int) $selectedVariant === $variant->id)
                                        <button type="button"
                                                wire:click="selectVariant({{ $variant->id }})"
                                                wire:key="variant-{{ $variant->id }}"
                                                style="border-radius: 6px; padding: 8px 16px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.15s; {{ $isSelected ? 'border: 1px solid #061153; background: #061153; color: #fff;' : 'border: 1px solid #d4d4d8; background: #fff; color: #061153;' }}">
                                            {{ $variant->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @php($inCart = (int) ($this->cartItem?->quantity ?? 0))
                        @php($atStockLimit = $inCart > 0 && $inCart >= $stock)

                        {{-- Add to bag (only when not yet in cart). Once added, the in-cart manager below handles +/− --}}
                        @if ($inCart === 0)
                            <form wire:submit="addToCart">
                                <button type="submit"
                                        @disabled($stock < 1)
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 56px; width: 100%; border-radius: 8px; background: #061153; color: #fff; font-size: 16px; font-weight: 600; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); {{ $stock < 1 ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                    </svg>
                                    {{ $stock < 1 ? __('Out of stock') : __('Add to Bag') }}
                                </button>
                            </form>
                        @endif

                        {{-- In-cart manager --}}
                        @if ($inCart > 0)
                            <div style="background: #f4f4f5; border-radius: 10px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9999px; background: #061153; color: #fff;">
                                        <i class="fa-solid fa-cart-shopping" style="font-size: 13px;"></i>
                                    </span>
                                    <div>
                                        <p style="margin: 0; font-size: 13px; color: #6b7280;">{{ __('In your cart') }}</p>
                                        <p style="margin: 2px 0 0; font-size: 14px; font-weight: 600; color: #061153;">
                                            {{ trans_choice(':n item|:n items', $inCart, ['n' => $inCart]) }}
                                            <span style="color: #6b7280; font-weight: 400;">
                                                · GH₵{{ number_format($inCart * $mainPrice, 2) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="display: inline-flex; height: 36px; border: 1px solid #d4d4d8; border-radius: 6px; overflow: hidden; background: #fff;">
                                        <button type="button" wire:click="decrementCartQuantity"
                                                style="width: 32px; background: transparent; border: none; cursor: pointer; font-size: 16px; color: #374151;">−</button>
                                        <div style="width: 40px; display: flex; align-items: center; justify-content: center; border-left: 1px solid #d4d4d8; border-right: 1px solid #d4d4d8; font-size: 14px; font-weight: 600; color: #061153;">
                                            {{ $inCart }}
                                        </div>
                                        <button type="button" wire:click="incrementCartQuantity"
                                                @disabled($inCart >= $stock)
                                                style="width: 32px; background: transparent; border: none; cursor: pointer; font-size: 16px; color: #374151; {{ $inCart >= $stock ? 'opacity: 0.4; cursor: not-allowed;' : '' }}">+</button>
                                    </div>
                                    <button type="button" wire:click="removeFromCart"
                                            wire:confirm="{{ __('Remove this item from your cart?') }}"
                                            title="{{ __('Remove from cart') }}"
                                            style="background: transparent; border: none; cursor: pointer; color: #9ca3af; font-size: 14px;"
                                            onmouseover="this.style.color='#ef4444'"
                                            onmouseout="this.style.color='#9ca3af'">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <a href="{{ route('shop.cart') }}" wire:navigate
                                       style="font-size: 13px; font-weight: 600; color: #061153; text-decoration: underline;">
                                        {{ __('View cart') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Meta info --}}
                        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 100px; color: #6b7280;">{{ __('SKU:') }}</span>
                                <span style="font-weight: 500; color: #061153;">{{ $selected?->sku ?? '—' }}</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 100px; color: #6b7280;">{{ __('Categories:') }}</span>
                                <a href="{{ route('shop.index', ['cat' => $product->category_id]) }}" wire:navigate
                                   style="font-weight: 500; color: #061153;">
                                    {{ $product->category->name }}
                                </a>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 100px; color: #6b7280;">{{ __('Share:') }}</span>
                                <div style="display: flex; align-items: center; gap: 12px; color: #4b5563;">
                                    <a href="#" aria-label="Facebook" style="color: inherit;"><i class="fa-brands fa-facebook-f"></i></a>
                                    <a href="#" aria-label="Twitter" style="color: inherit;"><i class="fa-brands fa-twitter"></i></a>
                                    <a href="#" aria-label="Instagram" style="color: inherit;"><i class="fa-brands fa-instagram"></i></a>
                                    <a href="#" aria-label="LinkedIn" style="color: inherit;"><i class="fa-brands fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
