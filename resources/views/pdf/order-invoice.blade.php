<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Invoice #:num', ['num' => $order->order_number]) }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #061153; margin: 0; padding: 24px; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #061153; }
        .muted { color: #6b7280; font-size: 11px; }
        .banner { background: #f4f6fb; border: 1px solid #e5e7eb; padding: 10px 14px; border-radius: 6px; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 10px 12px; text-align: left; }
        th { background: #061153; color: #fff; font-weight: 700; }
        .right { text-align: right; }
        .totals { margin-top: 20px; width: 280px; margin-left: auto; }
        .totals td { border: none; padding: 6px 0; }
        .totals tr.grand td { font-size: 14px; font-weight: 700; border-top: 2px solid #061153; padding-top: 10px; }
        .provisional { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 10px 12px; border-radius: 6px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}</h1>
    <p class="muted">{{ __('Tax invoice / Receipt') }}</p>

    @if ($order->status === 'pending_payment')
        <div class="provisional">{{ __('PAYMENT PENDING — This document is provisional until payment is confirmed.') }}</div>
    @endif

    <div class="banner">
        <strong>{{ __('Invoice #:num', ['num' => $order->order_number]) }}</strong><br>
        <span class="muted">{{ __('Date: :d', ['d' => $order->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i')]) }}</span><br>
        <span class="muted">{{ __('Status: :s', ['s' => strtoupper($order->status)]) }}</span>
    </div>

    <table style="border: none;">
        <tr>
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>{{ __('Bill to') }}</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_email }}<br>
                @if ($order->customer_phone)
                    {{ $order->customer_phone }}<br>
                @endif
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>{{ __('Deliver to') }}</strong><br>
                {{ $order->delivery_zone }}<br>
                {!! nl2br(e($order->shipping_address)) !!}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th class="right">{{ __('Qty') }}</th>
                <th class="right">{{ __('Unit') }}</th>
                <th class="right">{{ __('Line') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }} — {{ $item->variant_name }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">GH₵ {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="right">GH₵ {{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>{{ __('Subtotal') }}</td>
            <td class="right">GH₵ {{ number_format((float) $order->subtotal, 2) }}</td>
        </tr>
        @if ((float) $order->discount_total > 0)
            <tr>
                <td>{{ __('Discount') }}</td>
                <td class="right">−GH₵ {{ number_format((float) $order->discount_total, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td>{{ __('Delivery') }}</td>
            <td class="right">GH₵ {{ number_format((float) $order->delivery_fee, 2) }}</td>
        </tr>
        <tr class="grand">
            <td>{{ __('Total') }}</td>
            <td class="right">GH₵ {{ number_format((float) $order->total, 2) }}</td>
        </tr>
    </table>

    @php($pay = $order->payments->sortByDesc('id')->first())
    @if ($pay)
        <p class="muted" style="margin-top: 24px;">
            {{ __('Payment: :provider — :status', ['provider' => $pay->provider, 'status' => $pay->status]) }}
            @if ($pay->transaction_reference)
                — {{ __('Ref: :r', ['r' => $pay->transaction_reference]) }}
            @endif
        </p>
    @endif

    <p class="muted" style="margin-top: 32px;">{{ __('Thank you for your business.') }}</p>
</body>
</html>
