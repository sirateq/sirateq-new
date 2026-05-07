<x-mail::message>
{{ __('Hello :name,', ['name' => $order->customer_name ?: $order->customer_email]) }}

<x-mail::panel>
{!! str($markdownBody)->markdown() !!}
</x-mail::panel>

**{{ __('Order') }}** #{{ $order->order_number }} · GH₵{{ number_format((float) $order->total, 2) }}

{{ __('Best regards') }},<br>
**{{ config('app.name') }}**
</x-mail::message>
