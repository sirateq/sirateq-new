@php
    $onSitePolicyUrl = url(route('shop.policies.returns', absolute: false));
    $countries = config('storefront.shipping_countries', ['Ghana']);
    $detailsUrl = config('storefront.return_policy_details_url');
@endphp

<x-layouts.app :title="__('Return policy')">
    <x-breadcrum-shop
        image="{{ asset('assets/images/server-woman.png') }}"
        title="{{ __('Return policy') }}"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('home')],
            ['label' => __('Shop'), 'url' => route('shop.index')],
            ['label' => __('Return policy')],
        ]"
    />

    <section class="relative overflow-hidden" style="padding-top: 28px; padding-bottom: 88px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 35%, #eef2ff 100%);">
        <div class="container" style="max-width: 820px;">
            <div class="rounded-2xl border bg-white p-8 shadow-lg shadow-zinc-900/5" style="border-color: #e5e7eb;">
                <p class="mb-2 text-sm font-semibold uppercase tracking-wide" style="color: #061153;">{{ __('Returns & refunds') }}</p>
                <h1 class="mb-4 text-2xl font-bold md:text-3xl" style="color: #061153;">{{ __('Return policy') }}</h1>

                <div class="product-rich-text mb-6 text-sm leading-relaxed" style="color: #4b5563;">
                    <p class="mb-4">
                        {{ __('This page describes how returns and refunds work for orders placed through our store. If you operate in more than one country or need exceptions (for example different rules by market), you can publish separate pages or update this text later to match.') }}
                    </p>

                    @if ($countries !== [])
                        <p class="mb-4">
                            <strong style="color: #061153;">{{ __('Countries we ship to') }}</strong><br>
                            {{ implode(', ', $countries) }}
                            <span class="mt-1 block text-xs" style="color: #6b7280;">{{ __('These are the countries or regions that currently share our shipping costs and delivery options.') }}</span>
                        </p>
                    @endif

                    <h2 class="mb-3 mt-6 text-lg font-semibold" style="color: #061153;">{{ __('How to request a return') }}</h2>
                    <ol class="mb-6 list-decimal space-y-2 ps-5">
                        <li>{{ __('Contact us with your order number and the item you wish to return.') }}</li>
                        <li>{{ __('We will confirm eligibility and, where applicable, share return instructions (including address or pickup details).') }}</li>
                        <li>{{ __('Send the item back in original condition where reasonably possible, including tags and packaging if they apply.') }}</li>
                        <li>{{ __('Once we receive and inspect the return, we will process a refund or exchange according to this policy.') }}</li>
                    </ol>

                    <h2 class="mb-3 text-lg font-semibold" style="color: #061153;">{{ __('Time limits') }}</h2>
                    <p class="mb-4">
                        {{ __('Unless we agree otherwise in writing, return requests should be made within a reasonable period after delivery. We will confirm the exact window when you contact us, so it matches your order and product type.') }}
                    </p>

                    <h2 class="mb-3 text-lg font-semibold" style="color: #061153;">{{ __('Refunds') }}</h2>
                    <p class="mb-4">
                        {{ __('Approved refunds are issued to the original payment method where possible. Processing times may depend on your bank or payment provider.') }}
                    </p>

                    @if (filled($detailsUrl))
                        <p class="mb-0">
                            <a href="{{ $detailsUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline" style="color: #061153;">{{ __('Additional policy details') }}</a>
                        </p>
                    @endif

                    <p class="mb-0 mt-6 rounded-lg border p-4 text-xs" style="border-color: #e5e7eb; background: #f9fafb; color: #6b7280;">
                        {{ __('For Google Merchant Center (or similar), use this return policy URL:') }}<br>
                        <code class="mt-1 inline-block break-all text-xs" style="color: #374151;">{{ $onSitePolicyUrl }}</code>
                        @if (filled(config('storefront.return_policy_url')))
                            <span class="mt-2 block">{{ __('You can also set STOREFRONT_RETURN_POLICY_URL in .env if you need to reference another canonical URL in feeds.') }}</span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('shop.index') }}" wire:navigate class="theme-btn br-30" style="display: inline-flex;">
                        <span class="link-effect">
                            <span class="effect-1">{{ __('Back to shop') }}</span>
                            <span class="effect-1">{{ __('Back to shop') }}</span>
                        </span>
                    </a>
                    <a href="{{ route('contact-us') }}" class="theme-btn br-30" style="display: inline-flex; opacity: 0.95;">
                        <span class="link-effect">
                            <span class="effect-1">{{ __('Contact us') }}</span>
                            <span class="effect-1">{{ __('Contact us') }}</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
