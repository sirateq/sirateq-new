<x-mail::message>
# {{ __('New order received') }}

**#{{ $order->order_number }}** — GH₵ **{{ number_format((float) $order->total, 2) }}**

<x-mail::panel>
**{{ __('Customer') }}:** {{ $order->customer_name }}<br>
**{{ __('Email') }}:** {{ $order->customer_email }}<br>
@if (filled($order->customer_phone))
**{{ __('Phone') }}:** {{ $order->customer_phone }}<br>
@endif
**{{ __('Payment') }}:** {{ str_replace('_', ' ', $order->payment_method) }}<br>
**{{ __('Status') }}:** {{ $order->status }}<br>
@if (filled($order->delivery_zone))
**{{ __('Zone') }}:** {{ $order->delivery_zone }}
@endif
</x-mail::panel>

**{{ __('Shipping address') }}**

<x-mail::panel>
{!! nl2br(e($order->shipping_address)) !!}
</x-mail::panel>

## {{ __('Line items') }}

@foreach ($order->items as $item)
- {{ $item->product_name }} ({{ $item->variant_name }}) × {{ $item->quantity }} @ GH₵ {{ number_format((float) $item->unit_price, 2) }} → **GH₵ {{ number_format((float) $item->line_total, 2) }}**
@endforeach

<x-mail::button :url="$adminOrderUrl" color="success">
{{ __('Open in admin') }}
</x-mail::button>

<x-mail::button :url="$orderUrl">
{{ __('Customer order page') }}
</x-mail::button>

<x-mail::button :url="$invoiceUrl">
{{ __('Customer invoice (PDF)') }}
</x-mail::button>

{{ __('The invoice PDF is attached for quick reference.') }}

### {{ __('Plain links') }}

@component('mail::table')
| | |
| --- | --- |
| {{ __('Admin') }} | [{{ $adminOrderUrl }}]({{ $adminOrderUrl }}) |
| {{ __('Storefront order') }} | [{{ $orderUrl }}]({{ $orderUrl }}) |
| {{ __('Invoice') }} | [{{ $invoiceUrl }}]({{ $invoiceUrl }}) |
@endcomponent

{{ __('Subtotal') }}: GH₵ {{ number_format((float) $order->subtotal, 2) }}
@if ((float) $order->discount_total > 0)
 · {{ __('Discount') }}: −GH₵ {{ number_format((float) $order->discount_total, 2) }}
@endif
 · {{ __('Delivery') }}: GH₵ {{ number_format((float) $order->delivery_fee, 2) }}
</x-mail::message>
