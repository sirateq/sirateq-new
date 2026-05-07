<x-mail::message>
# {{ __('Thanks, :name!', ['name' => $order->customer_name]) }}

{{ __('Your order **#:number** has been confirmed.', ['number' => $order->order_number]) }}

<x-mail::panel>
**{{ __('Total') }}:** GH₵ {{ number_format((float) $order->total, 2) }}<br>
**{{ __('Payment') }}:** {{ str_replace('_', ' ', $order->payment_method) }}<br>
@if (filled($order->delivery_zone))
**{{ __('Delivery') }}:** {{ $order->delivery_zone }}
@endif
</x-mail::panel>

## {{ __('Items') }}

@foreach ($order->items as $item)
- {{ $item->product_name }} ({{ $item->variant_name }}) × **{{ $item->quantity }}** — GH₵ {{ number_format((float) $item->line_total, 2) }}
@endforeach

<x-mail::button :url="$orderUrl">
{{ __('View your order') }}
</x-mail::button>

<x-mail::button :url="$invoiceUrl">
{{ __('Download invoice (PDF)') }}
</x-mail::button>

{{ __('Your invoice is also attached to this email as a PDF.') }}

### {{ __('Plain links') }}

@component('mail::table')
| | |
| --- | --- |
| {{ __('Order') }} | [{{ $orderUrl }}]({{ $orderUrl }}) |
| {{ __('Invoice') }} | [{{ $invoiceUrl }}]({{ $invoiceUrl }}) |
@endcomponent

{{ __('If you checked out as a guest, use these links or the attachment — they work without signing in while they remain valid.') }}

{{ __('We will contact you about delivery using the phone number you provided.') }}

{{ __('Best regards') }},<br>
**{{ config('app.name') }}**
</x-mail::message>
