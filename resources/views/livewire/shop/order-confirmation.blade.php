<div>
    @assets
        <script src="https://js.paystack.co/v1/inline.js" defer></script>
    @endassets

    <x-breadcrum-shop
        image="{{ asset('assets/images/server-woman.png') }}"
        :title="__('Order #:number', ['number' => $order->order_number])"
        :breadcrumbs="[
            ['label' => __('Shop'), 'url' => route('shop.index')],
            ['label' => '#' . $order->order_number, 'url' => null],
        ]"
    />

    <section class="bg-light order-confirmation-page" style="padding-top: 48px; padding-bottom: 80px;">
        <div class="container" style="max-width: 1180px;">
            <div style="display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 28px;">
                <div>
                    @if ($order->status === 'placed')
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: rgba(16, 185, 129, 0.12); color: #047857; font-size: 13px; font-weight: 700; margin-bottom: 10px;">
                            <i class="fa-solid fa-check"></i> {{ __('Order confirmed') }}
                        </div>
                    @elseif ($order->status === 'pending_payment')
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); color: #b45309; font-size: 13px; font-weight: 700; margin-bottom: 10px;">
                            <i class="fa-solid fa-clock"></i> {{ __('Awaiting payment') }}
                        </div>
                    @endif
                    <h1 style="font-size: 28px; font-weight: 700; color: #061153; margin: 0;">
                        {{ __('Thank you, :name', ['name' => $order->customer_name]) }}
                    </h1>
                    <p style="margin: 8px 0 0; font-size: 14px; color: #6b7280; max-width: 520px;">
                        @if ($order->status === 'placed')
                            {{ __('We have your order and will keep you updated. You can download your invoice below.') }}
                        @else
                            {{ __('Complete payment to finalize your order. You can still download a provisional invoice.') }}
                        @endif
                    </p>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <a href="{{ route('shop.orders.invoice', $order) }}"
                       style="display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 18px; border-radius: 10px; border: 1px solid #061153; background: #fff; color: #061153; font-size: 14px; font-weight: 600; text-decoration: none;"
                       onmouseover="this.style.background='#061153'; this.style.color='#fff';"
                       onmouseout="this.style.background='#fff'; this.style.color='#061153';">
                        <i class="fa-solid fa-file-pdf"></i>
                        {{ __('Download invoice') }}
                    </a>
                    <a href="{{ route('shop.index') }}" wire:navigate
                       style="display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 18px; border-radius: 10px; background: #061153; color: #fff; font-size: 14px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 14px rgba(6, 17, 83, 0.2);"
                       onmouseover="this.style.background='#1053f3';"
                       onmouseout="this.style.background='#061153';">
                        <i class="fa-solid fa-bag-shopping"></i>
                        {{ __('Continue shopping') }}
                    </a>
                </div>
            </div>

            <x-shop.order-timeline :order="$order" />

            @if ($verifying)
                <div style="display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 12px; background: rgba(16, 83, 243, 0.08); border: 1px solid rgba(16, 83, 243, 0.25); margin-bottom: 24px;">
                    <span style="display: inline-flex; color: #1053f3;"><i class="fa-solid fa-circle-notch fa-spin fa-lg"></i></span>
                    <div>
                        <p style="margin: 0; font-size: 15px; font-weight: 700; color: #061153;">{{ __('Verifying your payment') }}</p>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #4b5563;">{{ __('Confirming this charge with Paystack…') }}</p>
                    </div>
                </div>
            @endif

            @if ($this->requiresPaystackVerification())
                @php($pay = $this->latestPaystackPayment)
                <div style="background: #fff; border-radius: 16px; padding: 24px 28px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.08); border: 1px solid #e5e7eb; margin-bottom: 24px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #061153; margin: 0 0 8px;">
                        {{ __('Complete payment') }}
                    </h2>
                    <p style="margin: 0 0 18px; font-size: 14px; color: #6b7280;">
                        {{ __('Pay securely with Paystack. If you already paid, use “Verify payment” to sync your order.') }}
                    </p>

                    @if ($verificationFailureMessage)
                        <div style="padding: 14px 16px; border-radius: 12px; background: #fef2f2; border: 1px solid #fecaca; margin-bottom: 18px;">
                            <p style="margin: 0; font-size: 14px; font-weight: 700; color: #991b1b;">{{ __('Payment could not be confirmed') }}</p>
                            <p style="margin: 8px 0 0; font-size: 13px; color: #7f1d1d;">{{ $verificationFailureMessage }}</p>
                        </div>
                    @endif

                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        @if ($pay && $pay->status === 'pending')
                            <button type="button"
                                    wire:click="openPaystackCheckout"
                                    wire:loading.attr="disabled"
                                    wire:target="openPaystackCheckout"
                                    style="flex: 1; min-width: 180px; height: 50px; border-radius: 10px; background: #061153; color: #fff; font-size: 15px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                                <span wire:loading.remove.inline-flex wire:target="openPaystackCheckout" style="align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fa-solid fa-lock"></i> {{ __('Open Paystack') }}
                                </span>
                                <span wire:loading.inline-flex wire:target="openPaystackCheckout" style="align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> {{ __('Please wait…') }}
                                </span>
                            </button>
                        @endif
                        @if ($pay && $pay->status === 'failed')
                            <button type="button"
                                    wire:click="retryPaystackPayment"
                                    wire:loading.attr="disabled"
                                    wire:target="retryPaystackPayment"
                                    style="flex: 1; min-width: 180px; height: 50px; border-radius: 10px; background: #1053f3; color: #fff; font-size: 15px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                                <span wire:loading.remove.inline-flex wire:target="retryPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fa-solid fa-rotate-right"></i> {{ __('Retry payment') }}
                                </span>
                                <span wire:loading.inline-flex wire:target="retryPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> {{ __('Please wait…') }}
                                </span>
                            </button>
                        @endif
                        <button type="button"
                                wire:click="verifyLatestPaystackPayment"
                                wire:loading.attr="disabled"
                                wire:target="verifyLatestPaystackPayment"
                                style="flex: 1; min-width: 180px; height: 50px; border-radius: 10px; background: #fff; color: #061153; font-size: 15px; font-weight: 700; border: 1px solid #e5e7eb; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                            <span wire:loading.remove.inline-flex wire:target="verifyLatestPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                <i class="fa-solid fa-circle-check"></i> {{ __('Verify payment') }}
                            </span>
                            <span wire:loading.inline-flex wire:target="verifyLatestPaystackPayment" style="align-items: center; justify-content: center; gap: 10px;">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> {{ __('Please wait…') }}
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="row gy-4">
                <div class="col-lg-8">
                    <div style="background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.06); border: 1px solid #e5e7eb;">
                        <h2 style="font-size: 18px; font-weight: 700; color: #061153; margin: 0 0 20px;">{{ __('Order summary') }}</h2>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach ($order->items as $item)
                                <div wire:key="order-line-{{ $item->id }}" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 14px 0; border-bottom: 1px solid #f4f4f5;">
                                    <div style="min-width: 0;">
                                        <p style="margin: 0; font-size: 15px; font-weight: 600; color: #061153;">{{ $item->product_name }}</p>
                                        <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">{{ $item->variant_name }} · {{ __('Qty: :n', ['n' => $item->quantity]) }}</p>
                                    </div>
                                    <span style="font-size: 15px; font-weight: 700; color: #061153; flex-shrink: 0;">GH₵ {{ number_format((float) $item->line_total, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 6px 0;">
                                <span>{{ __('Subtotal') }}</span>
                                <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format((float) $order->subtotal, 2) }}</span>
                            </div>
                            @if ((float) $order->discount_total > 0)
                                <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 6px 0;">
                                    <span>{{ __('Discount') }}</span>
                                    <span style="font-weight: 600; color: #059669;">−GH₵ {{ number_format((float) $order->discount_total, 2) }}</span>
                                </div>
                            @endif
                            <div style="display: flex; justify-content: space-between; font-size: 14px; color: #4b5563; padding: 6px 0;">
                                <span>{{ __('Delivery fee') }}</span>
                                <span style="font-weight: 600; color: #061153;">GH₵ {{ number_format((float) $order->delivery_fee, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 14px; border-top: 2px solid #061153;">
                                <span style="font-size: 15px; font-weight: 700; color: #061153;">{{ __('Total') }}</span>
                                <span style="font-size: 22px; font-weight: 700; color: #1053f3;">GH₵ {{ number_format((float) $order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div style="background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.06); border: 1px solid #e5e7eb; position: sticky; top: 24px;">
                        <h2 style="font-size: 16px; font-weight: 700; color: #061153; margin: 0 0 16px;">{{ __('Details') }}</h2>
                        <dl style="margin: 0; font-size: 14px;">
                            <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Status') }}</dt>
                            <dd style="margin: 4px 0 0; color: #061153; font-weight: 700;">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</dd>
                            <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Email') }}</dt>
                            <dd style="margin: 4px 0 0; color: #061153;">{{ $order->customer_email }}</dd>
                            @if ($order->customer_phone)
                                <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Phone') }}</dt>
                                <dd style="margin: 4px 0 0; color: #061153;">{{ $order->customer_phone }}</dd>
                            @endif
                            <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Delivery') }}</dt>
                            <dd style="margin: 4px 0 0; color: #061153;">{{ $order->delivery_zone }}</dd>
                            <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Address') }}</dt>
                            <dd style="margin: 4px 0 0; color: #061153; white-space: pre-wrap;">{{ $order->shipping_address }}</dd>
                            <dt style="color: #6b7280; font-weight: 600; margin-top: 12px;">{{ __('Payment') }}</dt>
                            <dd style="margin: 4px 0 0; color: #061153;">{{ str_replace('_', ' ', $order->payment_method) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-shop.paystack-livewire-bridge />
</div>
