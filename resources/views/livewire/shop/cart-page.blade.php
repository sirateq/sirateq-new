<div>
    <x-breadcrum-shop
        title="{{ __('Shopping Cart') }}"
        image="{{ asset('assets/images/server-woman.png') }}"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('home')],
            ['label' => __('Shop'), 'url' => route('shop.index')],
            ['label' => __('Shopping Cart')],
        ]"
    />

    <section class="bg-light" style="padding-top: 60px; padding-bottom: 80px;">
        <div class="container">
            @if (! $this->cart || $this->cart->items->isEmpty())
                <div style="background: #fff; border-radius: 16px; padding: 60px 20px; text-align: center; max-width: 720px; margin: 0 auto;">
                    <div style="display: inline-flex; width: 80px; height: 80px; align-items: center; justify-content: center; border-radius: 9999px; background: #f4f4f5; color: #71717a; margin-bottom: 16px;">
                        <i class="fa-solid fa-cart-shopping" style="font-size: 28px;"></i>
                    </div>
                    <h2 style="font-size: 20px; font-weight: 600; color: #061153; margin: 0 0 8px;">{{ __('Your cart is empty') }}</h2>
                    <p style="color: #6b7280; margin: 0 0 20px;">{{ __('Browse our shop and add items you like.') }}</p>
                    <a href="{{ route('shop.index') }}" wire:navigate
                       style="display: inline-flex; align-items: center; gap: 8px; height: 48px; padding: 0 24px; border-radius: 8px; background: #061153; color: #fff; font-weight: 600; text-decoration: none;">
                        <i class="fa-solid fa-arrow-left"></i>
                        {{ __('Continue shopping') }}
                    </a>
                </div>
            @else
                <div style="max-width: 960px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    {{-- Table header --}}
                    <div style="display: grid; grid-template-columns: 1fr 200px 140px 40px; gap: 16px; padding: 16px 24px; background: #fafafa; border-bottom: 1px solid #e5e7eb; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; color: #6b7280; text-transform: uppercase;">
                        <div>{{ __('Products') }}</div>
                        <div style="text-align: center;">{{ __('Quantity') }}</div>
                        <div style="text-align: right;">{{ __('Price') }}</div>
                        <div></div>
                    </div>

                    {{-- Cart items --}}
                    @foreach ($this->cart->items as $item)
                        @php($variant = $item->variant)
                        @php($product = $variant->product)
                        @php($mainUrl = $product->main_image_url)
                        @php($stock = (int) optional($variant->inventoryItem)->quantity)
                        @php($lineTotal = (float) $item->unit_price * $item->quantity)
                        <div wire:key="cart-item-{{ $item->id }}"
                             style="display: grid; grid-template-columns: 1fr 200px 140px 40px; gap: 16px; padding: 20px 24px; align-items: center; border-bottom: 1px solid #f3f4f6;">
                            {{-- Product info --}}
                            <div style="display: flex; align-items: center; gap: 16px; min-width: 0;">
                                <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                                   style="flex: 0 0 auto; width: 64px; height: 64px; border-radius: 8px; background: #f4f4f5; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                                    @if ($mainUrl)
                                        <img src="{{ $mainUrl }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="fa-regular fa-image" style="color: #a1a1aa;"></i>
                                    @endif
                                </a>
                                <div style="min-width: 0;">
                                    <a href="{{ route('shop.products.show', $product->slug) }}" wire:navigate
                                       style="display: block; font-size: 15px; font-weight: 600; color: #061153; text-decoration: none; line-height: 1.3;">
                                        {{ $product->name }}
                                    </a>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">{{ $variant->name }}</p>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #061153; font-weight: 600;">
                                        GH₵{{ number_format((float) $item->unit_price, 2) }}
                                    </p>
                                    @if ($stock <= 0)
                                        <span style="display: inline-block; margin-top: 6px; background: #6b7280; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 0.08em; padding: 4px 8px; border-radius: 4px; text-transform: uppercase;">
                                            {{ __('Out of stock') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Quantity stepper --}}
                            <div style="display: flex; justify-content: center;">
                                <div style="display: inline-flex; height: 40px; border: 1px solid #d4d4d8; border-radius: 6px; overflow: hidden; background: #fff;">
                                    <button type="button"
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            style="width: 36px; background: #fff; border: none; cursor: pointer; font-size: 16px; color: #374151;"
                                            @disabled($item->quantity <= 1)>−</button>
                                    <div style="width: 48px; display: flex; align-items: center; justify-content: center; border-left: 1px solid #d4d4d8; border-right: 1px solid #d4d4d8; font-size: 14px; font-weight: 600; color: #061153;">
                                        {{ $item->quantity }}
                                    </div>
                                    <button type="button"
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            style="width: 36px; background: #fff; border: none; cursor: pointer; font-size: 16px; color: #374151;">+</button>
                                </div>
                            </div>

                            {{-- Line price --}}
                            <div style="text-align: right; font-size: 15px; font-weight: 700; color: #061153;">
                                GH₵{{ number_format($lineTotal, 2) }}
                            </div>

                            {{-- Remove --}}
                            <div style="text-align: right;">
                                <button type="button"
                                        wire:click="removeItem({{ $item->id }})"
                                        wire:confirm="{{ __('Remove this item?') }}"
                                        title="{{ __('Remove') }}"
                                        style="background: transparent; border: none; cursor: pointer; color: #9ca3af; font-size: 16px; padding: 4px;"
                                        onmouseover="this.style.color='#ef4444'"
                                        onmouseout="this.style.color='#9ca3af'">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Action row --}}
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; background: #fafafa;">
                        <a href="{{ route('shop.index') }}" wire:navigate
                           style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #061153; text-decoration: none;">
                            <i class="fa-solid fa-arrow-left" style="font-size: 12px;"></i>
                            {{ __('Continue Shopping') }}
                        </a>
                        <button type="button"
                                wire:click="clearCart"
                                wire:confirm="{{ __('Clear all items from your cart?') }}"
                                style="background: transparent; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #061153;">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('Clear Shopping Cart') }}
                        </button>
                    </div>
                </div>

                {{-- Coupon + summary --}}
                <div class="row gy-30 mt-5" style="max-width: 960px; margin: 40px auto 0;">
                    {{-- Coupon Discount --}}
                    <div class="col-lg-6">
                        <div style="background: #fff; border-radius: 12px; padding: 24px; height: 100%;">
                            <h3 style="font-size: 18px; font-weight: 700; color: #061153; margin: 0 0 8px;">{{ __('Coupon Discount') }}</h3>
                            <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">{{ __('Enter your coupon code if you have one.') }}</p>

                            <div style="display: flex; gap: 8px;">
                                <input type="text"
                                       wire:model="couponCode"
                                       wire:keydown.enter="applyCoupon"
                                       @disabled($appliedCoupon)
                                       placeholder="{{ __('Enter coupon code here') }}"
                                       style="flex: 1; height: 44px; padding: 0 14px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fafafa; font-size: 14px; color: #061153; outline: none; text-transform: uppercase; {{ $appliedCoupon ? 'opacity: 0.6;' : '' }}">
                                @if ($appliedCoupon)
                                    <button type="button"
                                            wire:click="removeCoupon"
                                            style="height: 44px; padding: 0 18px; border-radius: 6px; background: transparent; color: #ef4444; font-size: 13px; font-weight: 600; border: 1px solid #fecaca; cursor: pointer;">
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>

                            <button type="button"
                                    wire:click="applyCoupon"
                                    @disabled($appliedCoupon)
                                    style="margin-top: 12px; width: 100%; height: 48px; border-radius: 6px; background: #061153; color: #fff; font-size: 14px; font-weight: 600; border: none; cursor: pointer; {{ $appliedCoupon ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                                {{ $appliedCoupon ? __('Coupon applied') : __('Apply coupon') }}
                            </button>

                            @if ($appliedCoupon)
                                <div style="margin-top: 14px; padding: 10px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 13px; color: #065f46;">
                                    <i class="fa-solid fa-circle-check"></i>
                                    {{ __('Code :code applied — you saved GH₵:amount', [
                                        'code' => $appliedCoupon,
                                        'amount' => number_format($appliedDiscount, 2),
                                    ]) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Order summary --}}
                    <div class="col-lg-6">
                        <div style="background: #fff; border-radius: 12px; padding: 24px; height: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 14px; color: #4b5563;">
                                <span>{{ __('Subtotal') }}</span>
                                <span style="font-weight: 600; color: #061153;">GH₵{{ number_format($this->cartSubtotal(), 2) }}</span>
                            </div>
                            @if ($appliedDiscount > 0)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 14px; color: #4b5563; border-bottom: 1px solid #e5e7eb;">
                                    <span>{{ __('Discount') }} <span style="color: #9ca3af;">({{ $appliedCoupon }})</span></span>
                                    <span style="font-weight: 600; color: #059669;">−GH₵{{ number_format($appliedDiscount, 2) }}</span>
                                </div>
                            @endif
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0 18px; font-size: 16px; color: #061153; font-weight: 700;">
                                <span>{{ __('Total:') }}</span>
                                <span style="font-size: 22px;">GH₵{{ number_format($this->total(), 2) }}</span>
                            </div>
                            <a href="{{ route('shop.checkout') }}" wire:navigate
                               style="display: flex; align-items: center; justify-content: center; height: 56px; border-radius: 8px; background: #061153; color: #fff; font-size: 16px; font-weight: 600; text-decoration: none; gap: 8px;">
                                {{ __('Check Out') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
