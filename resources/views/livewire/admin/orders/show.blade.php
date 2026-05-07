@php
    $paymentMethodLabel = match ($order->payment_method) {
        'pay_now' => __('Pay now'),
        'pay_on_delivery' => __('Pay on delivery'),
        default => str_replace('_', ' ', (string) $order->payment_method),
    };
@endphp

<section class="w-full">
    <x-admin.layout
        :heading="'#' . $order->order_number"
        :subheading="__(':date · :total — :customer', [
            'date' => $order->created_at?->timezone(config('app.timezone'))->format('M j, Y · g:i A') ?? '—',
            'total' => 'GH₵'.number_format((float) $order->total, 2),
            'customer' => $order->customer_name ?: $order->customer_email,
        ])"
        icon="receipt-percent"
    >
        <x-slot name="actions">
            <flux:button variant="ghost" icon="arrow-left" :href="route('admin.orders.index')" wire:navigate>
                {{ __('Orders') }}
            </flux:button>
            <flux:button variant="ghost" icon="arrow-top-right-on-square" :href="$this->customerStorefrontUrl" target="_blank" rel="noopener noreferrer">
                {{ __('Customer page') }}
            </flux:button>
            <flux:button variant="ghost" icon="arrow-down-tray" :href="route('admin.orders.invoice', $order)">
                {{ __('Invoice PDF') }}
            </flux:button>
        </x-slot>

        {{-- Order hero --}}
        <div class="mb-6 overflow-hidden rounded-2xl border border-zinc-200/80 bg-gradient-to-br from-zinc-50 via-white to-zinc-50/80 shadow-sm dark:border-zinc-700/70 dark:from-zinc-900 dark:via-zinc-900/95 dark:to-zinc-950/90">
            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div class="min-w-0 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-lg font-semibold tracking-tight text-zinc-900 dark:text-white sm:text-xl">
                            #{{ $order->order_number }}
                        </span>
                        <flux:badge size="sm" :color="$this->statusColor($order->status)" inset="top bottom">
                            {{ str_replace('_', ' ', ucfirst($order->status)) }}
                        </flux:badge>
                        <flux:badge size="sm" color="zinc" inset="top bottom">
                            {{ $paymentMethodLabel }}
                        </flux:badge>
                    </div>
                    <flux:text size="sm" class="text-zinc-500">
                        {{ __('Created :absolute (:relative)', [
                            'absolute' => $order->created_at?->timezone(config('app.timezone'))->format('D, M j, Y g:i A') ?? '—',
                            'relative' => $order->created_at?->diffForHumans() ?? '—',
                        ]) }}
                    </flux:text>
                </div>
                <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Order total') }}</flux:text>
                    <span class="text-2xl font-bold tabular-nums tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        GH₵{{ number_format((float) $order->total, 2) }}
                    </span>
                </div>
            </div>
        </div>

        @if ($order->status === 'pending_payment')
            <flux:callout variant="warning" icon="exclamation-circle" class="mb-6">
                {{ __('This order is still awaiting payment. The customer may complete checkout in Paystack.') }}
            </flux:callout>
        @endif

        <div class="grid gap-6 lg:grid-cols-12">
            {{-- Main column --}}
            <div class="space-y-6 lg:col-span-8">
                {{-- Money summary --}}
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-zinc-200/70 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">{{ __('Subtotal') }}</flux:text>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-zinc-900 dark:text-white">GH₵{{ number_format((float) $order->subtotal, 2) }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200/70 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">{{ __('Discount') }}</flux:text>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">
                            @if ((float) $order->discount_total > 0)
                                −GH₵{{ number_format((float) $order->discount_total, 2) }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="rounded-xl border border-zinc-200/70 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                        <flux:text size="sm" class="text-zinc-500">{{ __('Delivery') }}</flux:text>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-zinc-900 dark:text-white">GH₵{{ number_format((float) $order->delivery_fee, 2) }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200/70 bg-white p-4 shadow-sm ring-1 ring-zinc-900/5 dark:border-zinc-700/60 dark:bg-zinc-900 dark:ring-white/10">
                        <flux:text size="sm" class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Total') }}</flux:text>
                        <p class="mt-1 text-lg font-bold tabular-nums text-zinc-900 dark:text-white">GH₵{{ number_format((float) $order->total, 2) }}</p>
                    </div>
                </div>

                @if ($order->coupon)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-200/80 bg-emerald-50/60 px-4 py-3 dark:border-emerald-800/50 dark:bg-emerald-950/30">
                        <div class="flex items-center gap-2">
                            <flux:icon name="ticket" class="size-5 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <flux:text class="font-medium text-emerald-900 dark:text-emerald-100">{{ __('Coupon applied') }}</flux:text>
                                <flux:text size="sm" class="text-emerald-800/90 dark:text-emerald-200/80">{{ $order->coupon->code }} · {{ $order->coupon->discount_percentage }}%</flux:text>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Line items --}}
                <div class="overflow-hidden rounded-2xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex flex-col gap-1 border-b border-zinc-200/70 px-5 py-4 dark:border-zinc-700/60 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <flux:heading size="lg">{{ __('Line items') }}</flux:heading>
                            <flux:subheading>{{ $order->items->count() }} {{ __('items') }}</flux:subheading>
                        </div>
                    </div>

                    <div class="hidden border-b border-zinc-100 bg-zinc-50/80 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400 md:grid md:grid-cols-12 md:gap-4">
                        <div class="md:col-span-5">{{ __('Product') }}</div>
                        <div class="md:col-span-2 md:text-end">{{ __('Unit') }}</div>
                        <div class="md:col-span-2 md:text-center">{{ __('Qty') }}</div>
                        <div class="md:col-span-3 md:text-end">{{ __('Line total') }}</div>
                    </div>

                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($order->items as $item)
                            <li class="px-5 py-4 md:grid md:grid-cols-12 md:items-center md:gap-4 md:py-3" wire:key="admin-order-item-{{ $item->id }}">
                                <div class="flex items-start gap-3 md:col-span-5">
                                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="cube" class="size-5 text-zinc-500 dark:text-zinc-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <flux:heading class="!text-sm !leading-snug">{{ $item->product_name }}</flux:heading>
                                        <flux:text size="sm" class="text-zinc-500">{{ $item->variant_name }}</flux:text>
                                        <flux:text size="sm" class="mt-1 text-zinc-500 md:hidden">
                                            {{ __(':qty × GH₵:unit', ['qty' => $item->quantity, 'unit' => number_format((float) $item->unit_price, 2)]) }}
                                        </flux:text>
                                    </div>
                                </div>
                                <div class="hidden text-end tabular-nums text-sm text-zinc-600 dark:text-zinc-300 md:col-span-2 md:block">
                                    GH₵{{ number_format((float) $item->unit_price, 2) }}
                                </div>
                                <div class="hidden text-center tabular-nums text-sm font-medium text-zinc-900 dark:text-white md:col-span-2 md:block">
                                    {{ $item->quantity }}
                                </div>
                                <div class="mt-3 text-end md:col-span-3 md:mt-0">
                                    <flux:text variant="strong" class="tabular-nums">GH₵{{ number_format((float) $item->line_total, 2) }}</flux:text>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex items-center justify-between border-t border-zinc-200/70 bg-zinc-50/50 px-5 py-4 dark:border-zinc-700/60 dark:bg-zinc-800/30">
                        <flux:heading size="md">{{ __('Total') }}</flux:heading>
                        <flux:heading size="lg" class="tabular-nums">GH₵{{ number_format((float) $order->total, 2) }}</flux:heading>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-4">{{ __('Customer') }}</flux:heading>
                    <div class="flex items-start gap-3">
                        <flux:avatar size="lg" :name="$order->customer_name ?? $order->customer_email" />
                        <div class="min-w-0 flex-1 space-y-2">
                            <div>
                                <flux:heading class="!text-base">{{ $order->customer_name ?: __('Guest') }}</flux:heading>
                                <flux:text size="sm" class="truncate text-zinc-500">{{ $order->customer_email }}</flux:text>
                            </div>
                            @if (filled($order->customer_phone))
                                <flux:text size="sm" class="tabular-nums text-zinc-600 dark:text-zinc-300">{{ $order->customer_phone }}</flux:text>
                            @endif
                            <div class="flex flex-wrap gap-2 pt-1">
                                <flux:button size="sm" variant="ghost" icon="envelope" :href="'mailto:'.$order->customer_email">
                                    {{ __('Email') }}
                                </flux:button>
                                @if (filled($order->customer_phone))
                                    <flux:button size="sm" variant="ghost" icon="phone" :href="'tel:'.$order->customer_phone">
                                        {{ __('Call') }}
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-3 flex items-center gap-2">
                        <flux:icon name="truck" class="size-5 text-zinc-500" />
                        {{ __('Delivery') }}
                    </flux:heading>
                    @if (filled($order->delivery_zone))
                        <flux:badge color="zinc" size="sm" class="mb-3">{{ $order->delivery_zone }}</flux:badge>
                    @endif
                    <div class="rounded-lg bg-zinc-50 px-3 py-3 text-sm leading-relaxed text-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300">
                        {{ $order->shipping_address }}
                    </div>
                </div>

                @if ($order->payments->isNotEmpty())
                    <div class="rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                        <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                            <flux:icon name="credit-card" class="size-5 text-zinc-500" />
                            {{ __('Payments') }}
                        </flux:heading>
                        <ul class="space-y-3">
                            @foreach ($order->payments as $payment)
                                <li class="rounded-xl border border-zinc-100 bg-zinc-50/80 px-3 py-3 dark:border-zinc-800 dark:bg-zinc-800/40" wire:key="admin-payment-{{ $payment->id }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <flux:text class="font-medium capitalize">{{ str_replace('_', ' ', $payment->provider) }}</flux:text>
                                            <flux:text size="sm" class="mt-0.5 font-mono text-xs text-zinc-500">{{ $payment->transaction_reference }}</flux:text>
                                        </div>
                                        <flux:badge size="sm" :color="match ($payment->status) {
                                            'paid' => 'green',
                                            'pending' => 'amber',
                                            'failed' => 'red',
                                            default => 'zinc',
                                        }" inset="top bottom">
                                            {{ ucfirst($payment->status) }}
                                        </flux:badge>
                                    </div>
                                    <flux:text variant="strong" class="mt-2 tabular-nums">GH₵{{ number_format((float) $payment->amount, 2) }}</flux:text>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-1">{{ __('Customer messages') }}</flux:heading>
                    <flux:subheading class="mb-4">{{ __('Send a custom email or SMS, or resend the standard order confirmation.') }}</flux:subheading>

                    <div class="space-y-6">
                        <div class="space-y-3 rounded-xl border border-zinc-100 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <flux:heading size="sm">{{ __('Custom email') }}</flux:heading>
                            <flux:input wire:model="customEmailSubject" :label="__('Subject')" :placeholder="__('e.g. Update on your order #123456')" />
                            <flux:textarea wire:model="customEmailBody" :label="__('Message')" rows="5" :placeholder="__('Markdown supported: **bold**, lists, links…')" />
                            <flux:button variant="primary" icon="paper-airplane" wire:click="sendCustomCustomerEmail" wire:loading.attr="disabled" wire:target="sendCustomCustomerEmail" class="w-full">
                                <span wire:loading.remove wire:target="sendCustomCustomerEmail">{{ __('Send email') }}</span>
                                <span wire:loading wire:target="sendCustomCustomerEmail">{{ __('Sending…') }}</span>
                            </flux:button>
                        </div>

                        <div class="space-y-3 rounded-xl border border-zinc-100 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <flux:heading size="sm">{{ __('Custom SMS') }}</flux:heading>
                            <flux:textarea wire:model="customSmsBody" :label="__('Text message')" rows="3" :placeholder="__('Short message to send via your SMS provider')" />
                            @if (! filled($order->customer_phone))
                                <flux:callout variant="warning">{{ __('No phone number on this order.') }}</flux:callout>
                            @endif
                            <flux:button variant="outline" icon="chat-bubble-left-right" wire:click="sendCustomCustomerSms" wire:loading.attr="disabled" wire:target="sendCustomCustomerSms" class="w-full" :disabled="! filled($order->customer_phone)">
                                <span wire:loading.remove wire:target="sendCustomCustomerSms">{{ __('Send SMS') }}</span>
                                <span wire:loading wire:target="sendCustomCustomerSms">{{ __('Sending…') }}</span>
                            </flux:button>
                        </div>

                        <flux:separator />

                        <flux:button variant="ghost" icon="arrow-path" wire:click="resendCustomerOrderNotice" wire:loading.attr="disabled" wire:target="resendCustomerOrderNotice" class="w-full">
                            <span wire:loading.remove wire:target="resendCustomerOrderNotice">{{ __('Resend standard order notice') }}</span>
                            <span wire:loading wire:target="resendCustomerOrderNotice">{{ __('Sending…') }}</span>
                        </flux:button>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200/70 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <flux:heading size="lg" class="mb-4">{{ __('Fulfillment') }}</flux:heading>
                    <form wire:submit="updateStatus" class="space-y-4">
                        <flux:select wire:model="status" :label="__('Order status')">
                            @foreach (\App\Livewire\Admin\Orders\Show::statusOptions() as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button type="submit" variant="primary" icon="check" class="w-full" wire:loading.attr="disabled" wire:target="updateStatus">
                            <span wire:loading.remove wire:target="updateStatus">{{ __('Save status') }}</span>
                            <span wire:loading wire:target="updateStatus">{{ __('Saving…') }}</span>
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>
    </x-admin.layout>
</section>
