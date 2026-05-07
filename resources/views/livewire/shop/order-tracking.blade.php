<div>
    <x-breadcrum-shop
        image="{{ asset('assets/images/server-woman.png') }}"
        :title="__('Track your order')"
        :breadcrumbs="[
            ['label' => __('Shop'), 'url' => route('shop.index')],
            ['label' => __('Track order'), 'url' => null],
        ]"
    />

    <section class="bg-light" style="padding-top: 48px; padding-bottom: 80px;">
        <div class="container" style="max-width: 560px;">
            <div style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.06), 0 8px 24px rgba(6, 17, 83, 0.08); border: 1px solid #e5e7eb;">
                <h1 style="font-size: 22px; font-weight: 700; color: #061153; margin: 0 0 8px;">
                    {{ __('Look up your order') }}
                </h1>
                <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280;">
                    {{ __('Enter the order number from your confirmation email and the email address you used at checkout.') }}
                </p>

                <form wire:submit="lookup" class="space-y-4" style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Order number') }}</label>
                        <input type="text" wire:model="order_number" placeholder="123456"
                               autocomplete="one-time-code"
                               style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('order_number') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 8px; font-size: 15px; font-family: ui-monospace, monospace; color: #061153;">
                        @error('order_number')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #061153; margin-bottom: 6px;">{{ __('Email address') }}</label>
                        <input type="email" wire:model="email" placeholder="you@example.com"
                               autocomplete="email"
                               style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid {{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }}; border-radius: 8px; font-size: 15px; color: #061153;">
                        @error('email')<p style="margin: 6px 0 0; font-size: 12px; color: #dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="lookup"
                            style="margin-top: 8px; width: 100%; height: 48px; border-radius: 10px; background: #061153; color: #fff; font-size: 15px; font-weight: 700; border: none; cursor: pointer;">
                        <span wire:loading.remove wire:target="lookup">{{ __('View order') }}</span>
                        <span wire:loading wire:target="lookup">{{ __('Please wait…') }}</span>
                    </button>
                </form>

                <p style="margin: 20px 0 0; font-size: 12px; color: #9ca3af; text-align: center;">
                    <a href="{{ route('shop.index') }}" wire:navigate style="color: #1053f3; text-decoration: none;">{{ __('Back to shop') }}</a>
                </p>
            </div>
        </div>
    </section>
</div>
