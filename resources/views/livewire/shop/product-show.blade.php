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
                        @if (filled(trim((string) $product->description)))
                            <div class="product-rich-text text-sm leading-relaxed text-[#4b5563]">
                                {!! $product->renderedDescriptionHtml() !!}
                            </div>
                        @else
                            <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin: 0;">
                                {{ __('No description yet.') }}
                            </p>
                        @endif

                        {{-- Option pickers (Color, Size, Gender, …) --}}
                        @if ($product->optionGroups->isNotEmpty())
                            @foreach ($product->optionGroups as $group)
                                <div wire:key="opt-grp-{{ $group->id }}">
                                    <p style="margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #061153;">
                                        {{ $group->name }}
                                    </p>
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach ($group->values as $value)
                                            @php
                                                $isSel = (int) ($selectedValueByGroupId[$group->id] ?? 0) === (int) $value->id;
                                                $avail = $this->isOptionValueAvailable($group->id, $value->id);
                                            @endphp
                                            <button type="button"
                                                    wire:click="selectOptionValue({{ $group->id }}, {{ $value->id }})"
                                                    title="{{ $value->label }}"
                                                    @disabled(! $avail)
                                                    style="display: inline-flex; align-items: center; gap: 8px; padding: {{ $group->display_type === 'swatch_image' ? '4px' : '8px 14px' }}; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;
                                                        border: 2px solid {{ $isSel ? '#061153' : '#e5e7eb' }};
                                                        background: {{ $isSel ? 'rgba(6,17,83,0.06)' : '#fff' }};
                                                        color: #061153;
                                                        opacity: {{ $avail ? '1' : '0.45' }};
                                                        {{ ! $avail ? 'cursor: not-allowed;' : '' }}">
                                                @if ($group->display_type === 'swatch_color' && filled($value->hex_color))
                                                    <span style="width: 22px; height: 22px; border-radius: 999px; border: 1px solid rgba(0,0,0,0.12); background: {{ $value->hex_color }}; flex-shrink: 0;"></span>
                                                @endif
                                                @if ($group->display_type === 'swatch_image' && $value->productImage)
                                                    <img src="{{ $value->productImage->url }}" alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px;">
                                                @endif
                                                <span>{{ $value->label }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @elseif ($product->variants->count() > 1)
                            <div>
                                <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #061153;">
                                    {{ __('Choose option') }}
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
                            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-start;">
                                <span style="width: 100px; flex-shrink: 0; color: #6b7280;">{{ __('Share:') }}</span>
                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; color: #4b5563;">
                                    <button type="button"
                                            wire:click="openNativeShare"
                                            title="{{ __('Share') }}"
                                            aria-label="{{ __('Share') }}"
                                            style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #061153; cursor: pointer;">
                                        <i class="fa-solid fa-share-nodes" aria-hidden="true"></i>
                                    </button>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrlEnc }}"
                                       target="_blank" rel="noopener noreferrer"
                                       title="{{ __('Share on Facebook') }}"
                                       aria-label="{{ __('Share on Facebook') }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: inherit;">
                                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrlEnc }}&text={{ $shareTitleEnc }}"
                                       target="_blank" rel="noopener noreferrer"
                                       title="{{ __('Share on X') }}"
                                       aria-label="{{ __('Share on X') }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: inherit;">
                                        <i class="fa-brands fa-twitter" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrlEnc }}"
                                       target="_blank" rel="noopener noreferrer"
                                       title="{{ __('Share on LinkedIn') }}"
                                       aria-label="{{ __('Share on LinkedIn') }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: inherit;">
                                        <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ $whatsappTextEnc }}"
                                       target="_blank" rel="noopener noreferrer"
                                       title="{{ __('Share on WhatsApp') }}"
                                       aria-label="{{ __('Share on WhatsApp') }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: inherit;">
                                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                                    </a>
                                    <a href="mailto:?subject={{ $mailtoSubjectEnc }}&body={{ $mailtoBodyEnc }}"
                                       title="{{ __('Share by email') }}"
                                       aria-label="{{ __('Share by email') }}"
                                       style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: inherit;">
                                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                    </a>
                                    <button type="button"
                                            wire:click="copyShareLink"
                                            title="{{ __('Copy link') }}"
                                            aria-label="{{ __('Copy link') }}"
                                            style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #061153; cursor: pointer;">
                                        <i class="fa-solid fa-link" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($this->youMayAlsoLike->isNotEmpty())
                <div class="mt-5 pt-5" style="border-top: 1px solid #e5e7eb;">
                    <h2 style="font-size: 22px; font-weight: 700; color: #061153; margin: 0 0 24px;">
                        {{ __('You may also like') }}
                    </h2>
                    <div class="row g-4">
                        @foreach ($this->youMayAlsoLike as $related)
                            @php($relUrl = $related->main_image_url)
                            @php($relOos = $related->isOutOfStock())
                            <div class="col-6 col-md-3" wire:key="related-{{ $related->id }}">
                                <a href="{{ route('shop.products.show', $related->slug) }}" wire:navigate
                                   class="d-block text-decoration-none h-100 rounded-3 border bg-white shadow-sm overflow-hidden transition"
                                   style="border-color: #e5e7eb; color: inherit;"
                                   onmouseover="this.style.borderColor='#1053f3'; this.style.boxShadow='0 8px 24px rgba(6,17,83,0.12)'"
                                   onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow=''">
                                    <div class="position-relative bg-light overflow-hidden" style="aspect-ratio: 1 / 1; {{ $relOos ? 'opacity: 0.9;' : '' }}">
                                        @if ($relUrl)
                                            <img src="{{ $relUrl }}" alt="{{ $related->name }}"
                                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                        @endif
                                        @if ($relOos)
                                            <span class="position-absolute top-0 start-0 m-2 badge rounded-pill px-2 py-1"
                                                  style="font-size: 10px; background: #dc2626; color: #fff;">
                                                {{ __('Out of stock') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-3 text-center">
                                        <p class="mb-1 fw-bold tabular-nums" style="font-size: 1.15rem; color: #061153;">
                                            {{ $related->storefrontVariantPriceLabel() }}
                                        </p>
                                        <p class="mb-0 small fw-semibold" style="color: #0f172a; line-height: 1.35;">
                                            {{ \Illuminate\Support\Str::limit($related->name, 56) }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
