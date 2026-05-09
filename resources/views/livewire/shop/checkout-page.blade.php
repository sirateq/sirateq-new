<div>
    @assets
        <script src="https://js.paystack.co/v1/inline.js" defer></script>
    @endassets

    <x-breadcrum-shop image="{{ asset('assets/images/server-woman.png') }}" />

    <style>
        .checkout-page input:focus,
        .checkout-page select:focus,
        .checkout-page textarea:focus {
            border-color: #1053f3 !important;
            box-shadow: 0 0 0 3px rgba(16, 83, 243, 0.12) !important;
        }
        .checkout-page label[data-radio]:hover {
            border-color: #1053f3 !important;
        }
        /* Swap primary CTA label ↔ loading (avoids Bootstrap / inline display fighting Livewire wire:loading) */
        .checkout-submit-primary .checkout-submit-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .checkout-submit-primary .checkout-submit-busy {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .checkout-submit-primary.is-checkout-loading .checkout-submit-label {
            display: none !important;
        }
        .checkout-submit-primary.is-checkout-loading .checkout-submit-busy {
            display: inline-flex !important;
        }
    </style>

    <section class="bg-light checkout-page" style="padding-top: 60px; padding-bottom: 80px;">
        <div class="container" style="max-width: 1180px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 28px;">
                <div>
                    <h1 style="font-size: 28px; font-weight: 700; color: #061153; margin: 0;">{{ __('Checkout') }}</h1>
                    <p style="margin: 4px 0 0; font-size: 14px; color: #6b7280;">
                        @if ($payNowFlowPhase !== 'form')
                            {{ __('Review your order and finish paying securely.') }}
                        @else
                            {{ __('Review your details and confirm your payment.') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('shop.cart') }}" wire:navigate
                   style="display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; color: #061153; font-size: 13px; font-weight: 600; text-decoration: none;"
                   onmouseover="this.style.borderColor='#1053f3'; this.style.color='#1053f3';"
                   onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#061153';">
                    <i class="fa-solid fa-arrow-left" style="font-size: 11px;"></i>
                    {{ __('Back to cart') }}
                </a>
            </div>

            @if ($payNowFlowPhase !== 'form')
                @php($pendingOrder = $this->pendingPaystackOrder)
                @if ($pendingOrder)
                    <div class="row justify-content-center gy-4">
                        <div class="col-lg-9 col-xl-8">
                            <div style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.08); border: 1px solid #e5e7eb;">
                                @if ($payNowFlowPhase === 'verifying')
                                    <div style="display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 12px; background: rgba(16, 83, 243, 0.08); border: 1px solid rgba(16, 83, 243, 0.25); margin-bottom: 24px;">
                                        <span style="display: inline-flex; color: #1053f3;"><i class="fa-solid fa-circle-notch fa-spin fa-lg"></i></span>
                                        <div>
                                            <p style="margin: 0; font-size: 15px; font-weight: 700; color: #061153;">{{ __('Verifying your payment') }}</p>
                                            <p style="margin: 4px 0 0; font-size: 13px; color: #4b5563;">{{ __('Hang tight — we are confirming this charge with Paystack before completing your order.') }}</p>
                                        </div>
                                    </div>
                                @elseif ($payNowFlowPhase === 'failed')
                                    <div style="padding: 16px 18px; border-radius: 12px; background: #fef2f2; border: 1px solid #fecaca; margin-bottom: 24px;">
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #991b1b;">{{ __('Payment could not be confirmed') }}</p>
                                        <p style="margin: 8px 0 0; font-size: 13px; color: #7f1d1d;">{{ $verificationFailureMessage }}</p>
                                        <p style="margin: 10px 0 0; font-size: 12px; color: #991b1b;">{{ __('You can retry safely — we will use a new reference for Paystack.') }}</p>
                                    </div>
                                @else
                                    <div style="padding: 16px 18px; border-radius: 12px; background: rgba(6, 17, 83, 0.04); border: 1px solid #e5e7eb; margin-bottom: 24px;">
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #061153;">{{ __('Complete payment in Paystack') }}</p>
                                        <p style="margin: 6px 0 0; font-size: 13px; color: #6b7280;">{{ __('A secure window should open. If you closed it, use “Open Paystack” below. Your cart stays reserved until payment succeeds.') }}</p>
                                    </div>
                                @endif

                                <div style="display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                                    <div>
                                        <p style="margin: 0; font-size: 12px; font-weight: 600; color: #1053f3; text-transform: uppercase; letter-spacing: 0.04em;">{{ __('Order') }}</p>
                                        <h2 style="margin: 6px 0 0; font-size: 24px; font-weight: 700; color: #061153;">#{{ $pendingOrder->order_number }}</h2>
                                        <p style="margin: 6px 0 0; font-size: 13px; color: #6b7280;">{{ $pendingOrder->customer_name }} · {{ $pendingOrder->customer_email }}</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="margin: 0; font-size: 12px; color: #6b7280;">{{ __('Total due') }}</p>
                                        <p style="margin: 4px 0 0; font-size: 22px; font-weight: 700; color: #1053f3;">GH₵ {{ number_format((float) $pendingOrder->total, 2) }}</p>
                                    </div>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p style="margin: 0 0 8px; font-size: 12px; font-weight: 600; color: #061153;">{{ __('Delivery') }}</p>
                                    <p style="margin: 0; font-size: 14px; color: #374151;">{{ $pendingOrder->delivery_zone }}</p>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #6b7280; white-space: pre-wrap;">{{ $pendingOrder->shipping_address }}</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #061153;">{{ __('Items') }}</p>
                                    <div style="display: flex; flex-direction: column; gap: 12px;">
                                        @foreach ($pendingOrder->items as $line)
                                            <div wire:key="pending-line-{{ $line->id }}" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f4f4f5;">
                                                <div style="min-width:0;">
                                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #061153;">{{ $line->product_name }}</p>
                                                    <p style="margin: 2px 0 0; font-size: 12px; color: #6b7280;">{{ $line->variant_name }} · {{ __('Qty: :n', ['n' => $line->quantity]) }}</p>
                                                </div>
                                                <span style="font-size: 14px; font-weight: 700; color: #061153; flex-shrink: 0;">GH₵ {{ number_format((float) $line->line_total, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div style="padding-top: 8px; border-top: 1px dashed #e5e7eb;">
                                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 4px 0;">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format((float) $pendingOrder->subtotal, 2) }}</span>
                                    </div>
                                    @if ((float) $pendingOrder->discount_total > 0)
                                        <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 4px 0;">
                                            <span>{{ __('Discount') }}</span>
                                            <span style="font-weight: 600; color: #059669;">−GH₵ {{ number_format((float) $pendingOrder->discount_total, 2) }}</span>
                                        </div>
                                    @endif
                                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 4px 0;">
                                        <span>{{ __('Delivery fee') }}</span>
                                        <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format((float) $pendingOrder->delivery_fee, 2) }}</span>
                                    </div>
                                </div>

                                @if (in_array($payNowFlowPhase, ['summary', 'failed'], true))
                                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px;">
                                        @if ($payNowFlowPhase === 'summary')
                                            <button type="button"
                                                    wire:click="openPaystackCheckout"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openPaystackCheckout"
                                                    style="flex: 1; min-width: 200px; height: 52px; border-radius: 10px; background: #061153; color: #fff; font-size: 15px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                                                <span wire:loading.remove.inline-flex wire:target="openPaystackCheckout" style="align-items: center; justify-content: center; gap: 10px;">
                                                    <i class="fa-solid fa-lock"></i> {{ __('Open Paystack') }}
                                                </span>
                                                <span wire:loading.inline-flex wire:target="openPaystackCheckout" style="align-items: center; justify-content: center; gap: 10px;">
                                                    <i class="fa-solid fa-circle-notch fa-spin"></i> {{ __('Please wait…') }}
                                                </span>
                                            </button>
                                        @endif
                                        @if ($payNowFlowPhase === 'failed')
                                            <button type="button"
                                                    wire:click="retryPaystackPayment"
                                                    wire:loading.attr="disabled"
                                                    wire:target="retryPaystackPayment"
                                                    style="flex: 1; min-width: 200px; height: 52px; border-radius: 10px; background: #1053f3; color: #fff; font-size: 15px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                                                <span wire:loading.remove.inline-flex wire:target="retryPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                                    <i class="fa-solid fa-rotate-right"></i> {{ __('Retry payment') }}
                                                </span>
                                                <span wire:loading.inline-flex wire:target="retryPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                                    <i class="fa-solid fa-circle-notch fa-spin"></i> {{ __('Please wait…') }}
                                                </span>
                                            </button>
                                        @endif
                                        <button type="button"
                                                wire:click="abandonPendingPaystackOrder"
                                                wire:confirm="{{ __('Discard this pending order and edit your cart again?') }}"
                                                wire:loading.attr="disabled"
                                                wire:target="abandonPendingPaystackOrder"
                                                style="height: 52px; padding: 0 22px; border-radius: 10px; background: #fff; color: #061153; font-size: 14px; font-weight: 600; border: 1px solid #e5e7eb; cursor: pointer;">
                                            <span wire:loading.remove.inline-flex wire:target="abandonPendingPaystackOrder" style="align-items: center; justify-content: center;">{{ __('Start over') }}</span>
                                            <span wire:loading.inline-flex wire:target="abandonPendingPaystackOrder" style="align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div style="background: #fff; border-radius: 16px; padding: 48px 24px; text-align: center;">
                        <p style="margin: 0 0 16px; color: #4b5563;">{{ __('This checkout session could not be loaded.') }}</p>
                        <button type="button"
                                wire:click="abandonPendingPaystackOrder"
                                style="height: 44px; padding: 0 24px; border-radius: 8px; background: #061153; color: #fff; font-weight: 600; border: none; cursor: pointer;">
                            {{ __('Back to checkout') }}
                        </button>
                    </div>
                @endif
            @elseif (! $this->cart || $this->cart->items->isEmpty())
                <div style="background: #fff; border-radius: 16px; padding: 60px 20px; text-align: center;">
                    <p style="font-size: 16px; color: #4b5563; margin: 0 0 16px;">{{ __('No cart found. Add products before checking out.') }}</p>
                    <a href="{{ route('shop.index') }}" wire:navigate
                       style="display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 24px; border-radius: 8px; background: #061153; color: #fff; text-decoration: none; font-weight: 600;">
                        {{ __('Browse the shop') }}
                    </a>
                </div>
            @else
                <form wire:submit="placeOrder">
                    <div class="row gy-30">
                        {{-- LEFT — Shipping Information --}}
                        <div class="col-lg-7">
                            <div style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.05);">
                                <div style="display: flex; align-items: center; gap: 10px; margin: 0 0 24px;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; background: #061153; color: #fff; font-size: 13px; font-weight: 700;">1</span>
                                    <h2 style="font-size: 20px; font-weight: 700; color: #061153; margin: 0;">{{ __('Billing Information') }}</h2>
                                </div>

                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Full Name') }}</label>
                                        <input type="text" wire:model.blur="name"
                                               placeholder="Kwame Mensah"
                                               style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('name') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 6px; background: #fff; font-size: 14px; color: #061153; outline: none;">
                                        @error('name')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">
                                            {{ __('Mobile Money Number') }}
                                            <span style="font-weight: 400; color: #6b7280;">{{ __('(for payment)') }}</span>
                                        </label>
                                        <input type="tel" wire:model.blur="phone"
                                               placeholder="0241234567"
                                               style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('phone') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 6px; background: #fff; font-size: 14px; color: #061153; outline: none;">
                                        <p style="margin: 6px 0 0; font-size: 12px; color: #6b7280;">
                                            <i class="fa-regular fa-circle-question"></i>
                                            {{ __("We'll confirm your active contact for delivery after payment.") }}
                                        </p>
                                        @error('phone')<p style="margin: 4px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Email Address') }}</label>
                                        <input type="email" wire:model.blur="email"
                                               placeholder="kwame@example.com"
                                               style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 6px; background: #fff; font-size: 14px; color: #061153; outline: none;">
                                        @error('email')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Delivery Zone') }}</label>
                                        <select wire:model.live="delivery_zone"
                                                style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('delivery_zone') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 6px; background: #fff; font-size: 14px; color: #061153; outline: none;">
                                            <option value="">{{ __('Select your area') }}</option>
                                            @foreach (\App\Livewire\Shop\CheckoutPage::DELIVERY_ZONES as $zoneKey => $zone)
                                                <option value="{{ $zoneKey }}">{{ $zone['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('delivery_zone')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="col-12">
                                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Specific Location / Landmark') }}</label>
                                        <textarea wire:model.blur="shipping_address" rows="3"
                                                  placeholder="e.g., Near the big mango tree, House No. 123"
                                                  style="width: 100%; padding: 12px 14px; border: 1px solid {{ $errors->has('shipping_address') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 6px; background: #fff; font-size: 14px; color: #061153; outline: none; resize: vertical;"></textarea>
                                        @error('shipping_address')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT — Order Summary + Payment --}}
                        <div class="col-lg-5">
                            <div style="background: #fff; border-radius: 16px; padding: 28px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.05);">
                                <div style="display: flex; align-items: center; gap: 10px; margin: 0 0 24px;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; background: #061153; color: #fff; font-size: 13px; font-weight: 700;">2</span>
                                    <h2 style="font-size: 20px; font-weight: 700; color: #061153; margin: 0;">{{ __('Order Summary') }}</h2>
                                </div>

                                {{-- Items --}}
                                <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 18px;">
                                    @foreach ($this->cart->items as $item)
                                        @php($product = $item->variant->product)
                                        @php($mainUrl = $product->main_image_url)
                                        <div wire:key="checkout-item-{{ $item->id }}"
                                             style="display: flex; align-items: center; gap: 12px;">
                                            <div style="flex: 0 0 auto; width: 56px; height: 56px; border-radius: 8px; background: #f4f4f5; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                @if ($mainUrl)
                                                    <img src="{{ $mainUrl }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <i class="fa-regular fa-image" style="color: #a1a1aa;"></i>
                                                @endif
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <p style="margin: 0; font-size: 14px; font-weight: 600; color: #061153;">{{ $product->name }}</p>
                                                <p style="margin: 2px 0 0; font-size: 12px; color: #6b7280;">
                                                    @if ($product->variants->count() > 1) {{ $item->variant->name }} · @endif
                                                    {{ __('Qty: :n', ['n' => $item->quantity]) }}
                                                </p>
                                            </div>
                                            <div style="font-size: 14px; font-weight: 700; color: #1053f3;">
                                                GH₵ {{ number_format((float) $item->unit_price * $item->quantity, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 14px; color: #4b5563;">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format($this->subtotal(), 2) }}</span>
                                    </div>
                                    @if ($appliedDiscount > 0)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 14px; color: #4b5563;">
                                            <span>{{ __('Discount') }} <span style="color: #9ca3af;">({{ $appliedCoupon }})</span></span>
                                            <span style="font-weight: 600; color: #059669;">−GH₵ {{ number_format($appliedDiscount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 14px; color: #4b5563;">
                                        <span>{{ __('Delivery Fee') }}</span>
                                        @if ($delivery_zone)
                                            <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format($this->deliveryFee(), 2) }}</span>
                                        @else
                                            <span style="font-weight: 600; color: #1053f3;">{{ __('Select Zone') }}</span>
                                        @endif
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0 4px; border-top: 1px solid #e5e7eb; margin-top: 8px;">
                                        <span style="font-size: 16px; font-weight: 700; color: #061153;">{{ __('Total') }}</span>
                                        <span style="font-size: 22px; font-weight: 700; color: #1053f3;">GH₵ {{ number_format($this->total(), 2) }}</span>
                                    </div>
                                </div>

                                {{-- Discount code --}}
                                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px dashed #e5e7eb;">
                                    <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 8px;">{{ __('Discount Code') }}</label>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="text"
                                               wire:model="coupon_code"
                                               wire:keydown.enter.prevent="applyCoupon"
                                               @disabled($appliedCoupon)
                                               placeholder="{{ __('Enter code') }}"
                                               style="flex: 1; height: 44px; padding: 0 14px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb; font-size: 14px; color: #061153; outline: none; text-transform: uppercase; {{ $appliedCoupon ? 'opacity: 0.7; background:#f4f4f5;' : '' }}">
                                        @if ($appliedCoupon)
                                            <button type="button"
                                                    wire:click="removeCoupon"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeCoupon"
                                                    style="height: 44px; padding: 0 18px; border-radius: 8px; background: #fff; color: #061153; font-size: 13px; font-weight: 600; border: 1px solid #e5e7eb; cursor: pointer;"
                                                    onmouseover="this.style.borderColor='#1053f3'; this.style.color='#1053f3';"
                                                    onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#061153';">
                                                <span wire:loading.remove wire:target="removeCoupon">{{ __('Remove') }}</span>
                                                <span wire:loading wire:target="removeCoupon"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                            </button>
                                        @else
                                            <button type="button"
                                                    wire:click="applyCoupon"
                                                    wire:loading.attr="disabled"
                                                    wire:target="applyCoupon"
                                                    style="height: 44px; padding: 0 22px; border-radius: 8px; background: #061153; color: #fff; font-size: 13px; font-weight: 700; border: none; cursor: pointer;"
                                                    onmouseover="this.style.background='#1053f3'"
                                                    onmouseout="this.style.background='#061153'">
                                                <span wire:loading.remove wire:target="applyCoupon">{{ __('Apply') }}</span>
                                                <span wire:loading wire:target="applyCoupon"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                            </button>
                                        @endif
                                    </div>
                                    @if ($appliedCoupon)
                                        <div style="margin-top: 10px; padding: 8px 12px; border-radius: 8px; background: #ecfdf5; border: 1px solid #a7f3d0; font-size: 12px; color: #065f46; font-weight: 600;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            {{ __('Code :code applied — saved GH₵:amount', [
                                                'code' => $appliedCoupon,
                                                'amount' => number_format($appliedDiscount, 2),
                                            ]) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Payment Method --}}
                            <div style="background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.05);">
                                <h3 style="font-size: 18px; font-weight: 700; color: #061153; margin: 0 0 16px;">{{ __('Payment Method') }}</h3>

                                <label data-radio
                                       style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 10px; border: 2px solid {{ $payment_method === 'pay_now' ? '#061153' : '#e5e7eb' }}; cursor: pointer; margin-bottom: 12px; position: relative; background: {{ $payment_method === 'pay_now' ? 'rgba(6,17,83,0.03)' : '#fff' }}; transition: all 0.15s;">
                                    <input type="radio" name="payment_method" value="pay_now"
                                           wire:click="setPaymentMethod('pay_now')"
                                           {{ $payment_method === 'pay_now' ? 'checked' : '' }}
                                           style="margin: 0; accent-color: #061153;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 83, 243, 0.1); color: #1053f3;">
                                        <i class="fa-regular fa-credit-card"></i>
                                    </span>
                                    <div style="flex: 1;">
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #061153;">{{ __('Pay Now') }}</p>
                                        <p style="margin: 2px 0 0; font-size: 12px; color: #6b7280;">{{ __('Mobile Money / Card · Faster Processing') }}</p>
                                    </div>
                                    <span style="position: absolute; top: -10px; right: 16px; background: #1053f3; color: #fff; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 4px; letter-spacing: 0.04em;">{{ __('RECOMMENDED') }}</span>
                                </label>

                                <label data-radio
                                       style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 10px; border: 2px solid {{ $payment_method === 'pay_on_delivery' ? '#061153' : '#e5e7eb' }}; cursor: pointer; background: {{ $payment_method === 'pay_on_delivery' ? 'rgba(6,17,83,0.03)' : '#fff' }}; transition: all 0.15s;">
                                    <input type="radio" name="payment_method" value="pay_on_delivery"
                                           wire:click="setPaymentMethod('pay_on_delivery')"
                                           {{ $payment_method === 'pay_on_delivery' ? 'checked' : '' }}
                                           style="margin: 0; accent-color: #061153;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: rgba(6, 17, 83, 0.08); color: #061153;">
                                        <i class="fa-solid fa-truck"></i>
                                    </span>
                                    <div style="flex: 1;">
                                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #061153;">{{ __('Pay on Delivery') }}</p>
                                        <p style="margin: 2px 0 0; font-size: 12px; color: #6b7280;">{{ __('Cash / MoMo on arrival') }}</p>
                                    </div>
                                </label>

                                @error('payment_method')<p style="margin: 8px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror

                                <button type="submit"
                                        wire:loading.class="is-checkout-loading"
                                        wire:target="placeOrder"
                                        wire:loading.attr="disabled"
                                        class="checkout-submit-primary"
                                        style="margin-top: 22px; width: 100%; height: 56px; border-radius: 10px; background: #061153; color: #fff; font-size: 16px; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 6px 18px rgba(6, 17, 83, 0.25); display: inline-flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.15s;"
                                        onmouseover="if (!this.disabled) { this.style.background='#1053f3'; this.style.boxShadow='0 8px 22px rgba(16,83,243,0.32)'; }"
                                        onmouseout="this.style.background='#061153'; this.style.boxShadow='0 6px 18px rgba(6,17,83,0.25)';">
                                    <span class="checkout-submit-label">
                                        @if ($payment_method === 'pay_on_delivery')
                                            <i class="fa-solid fa-bag-shopping"></i>
                                            {{ __('Place Order') }}
                                        @else
                                            <i class="fa-solid fa-lock"></i>
                                            {{ __('Proceed to Payment') }}
                                        @endif
                                    </span>
                                    <span class="checkout-submit-busy">
                                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                                        {{ __('Please wait…') }}
                                    </span>
                                </button>

                                <p style="margin: 14px 0 0; font-size: 12px; color: #6b7280; text-align: center;">
                                    <a href="{{ route('shop.policies.returns') }}" wire:navigate style="color: #061153; font-weight: 600; text-decoration: underline;">{{ __('Return policy') }}</a>
                                    <span style="color: #d1d5db;">·</span>
                                    <span>{{ __('By placing an order you agree to our return and refund terms.') }}</span>
                                </p>

                                <p style="margin: 14px 0 0; font-size: 11px; color: #9ca3af; text-align: center;">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    {{ __('Secure checkout · Your details are encrypted.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>

    <x-shop.paystack-livewire-bridge />
</div>
