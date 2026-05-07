<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Invoice #:num', ['num' => $order->order_number]) }}</title>
    @php
        $accent = $invoiceConfig['accent'] ?? '#C15B3A';
        $company = $invoiceConfig['company'] ?? [];
        $dueDays = (int) ($invoiceConfig['payment_due_days'] ?? 14);
        $tz = config('app.timezone');
        $invoiceDate = $order->created_at->copy()->timezone($tz);
        $dueDate = $invoiceDate->copy()->addDays($dueDays);
    @endphp
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
            padding: 28px 32px 40px;
            line-height: 1.45;
        }
        .accent { color: {{ $accent }}; }
        .accent-bg { background-color: {{ $accent }}; color: #ffffff; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-table td { vertical-align: top; border: none; padding: 0; }
        .company-name {
            font-size: 13px;
            font-weight: 700;
            color: #111;
            margin-bottom: 6px;
        }
        .company-lines { color: #333; font-size: 10.5px; }
        .company-lines a { color: #333; text-decoration: none; }
        .logo-wrap {
            display: inline-block;
            text-align: center;
            border: 1px solid #d4d4d4;
            border-radius: 6px;
            padding: 10px 12px;
            min-width: 120px;
            min-height: 56px;
        }
        .logo-wrap img { max-height: 72px; max-width: 200px; display: block; margin: 0 auto; }
        .invoice-h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: {{ $accent }};
            text-align: right;
            margin: 18px 0 22px;
            clear: both;
        }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; border: none; padding: 0; width: 50%; }
        .billto-label {
            font-weight: 700;
            color: {{ $accent }};
            margin-bottom: 8px;
            font-size: 11px;
        }
        .customer-name { font-weight: 700; font-size: 11px; color: #111; margin-bottom: 4px; }
        .meta-right { text-align: right; font-size: 10.5px; }
        .meta-right .row { margin-bottom: 5px; }
        .meta-right .lbl { color: {{ $accent }}; font-weight: 700; display: inline-block; min-width: 92px; text-align: right; margin-right: 8px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .items-table thead th {
            background-color: {{ $accent }};
            color: #fff;
            font-weight: 700;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .items-table thead th:nth-child(1) { width: 11%; }
        .items-table thead th:nth-child(3),
        .items-table thead th:nth-child(4) { text-align: right; }
        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e5e5;
            vertical-align: top;
        }
        .items-table tbody td:nth-child(3),
        .items-table tbody td:nth-child(4) { text-align: right; white-space: nowrap; }
        .items-table tbody tr:last-child td { border-bottom: 2px solid #1a1a1a; }
        .totals-wrap { width: 100%; margin-top: 0; }
        .totals-inner { width: 280px; margin-left: auto; margin-top: 12px; }
        .totals-inner table { width: 100%; border-collapse: collapse; }
        .totals-inner td { border: none; padding: 5px 0; font-size: 10.5px; }
        .totals-inner td:last-child { text-align: right; white-space: nowrap; }
        .totals-inner tr.grand td {
            font-weight: 700;
            font-size: 12px;
            color: {{ $accent }};
            border-top: 1px solid {{ $accent }};
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid {{ $accent }};
        }
        .terms { margin-top: 36px; max-width: 90%; }
        .terms h3 {
            color: {{ $accent }};
            font-size: 11px;
            font-weight: 700;
            margin: 0 0 8px;
        }
        .terms p { margin: 0 0 6px; color: #444; font-size: 10px; }
        .provisional {
            background: #fff8f0;
            border: 1px solid {{ $accent }};
            color: #5c3318;
            padding: 12px 14px;
            border-radius: 6px;
            font-weight: 700;
            margin-bottom: 18px;
            font-size: 10.5px;
        }
        .payment-note { margin-top: 20px; font-size: 9.5px; color: #555; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-name">{{ $company['name'] ?? config('app.name') }}</div>
                <div class="company-lines">
                    {{ $company['address'] ?? '' }}<br>
                    <a href="mailto:{{ $company['email'] ?? '' }}">{{ $company['email'] ?? '' }}</a><br>
                    {{ $company['phone'] ?? '' }}
                </div>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <div class="logo-wrap">
                    @if (! empty($logoBase64))
                        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="">
                    @else
                        <span class="muted" style="font-size:9px;color:#888;">{{ __('Logo') }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="invoice-h1">{{ __('INVOICE') }}</div>

    @if ($order->status === 'pending_payment')
        <div class="provisional">{{ __('PAYMENT PENDING — This document is provisional until payment is confirmed.') }}</div>
    @endif

    <table class="meta-table">
        <tr>
            <td>
                <div class="billto-label">{{ __('Bill To') }}</div>
                <div class="customer-name">{{ $order->customer_name }}</div>
                <div class="company-lines">
                    @if ($order->shipping_address)
                        {!! nl2br(e($order->shipping_address)) !!}<br>
                    @endif
                    {{ $order->customer_email }}<br>
                    @if ($order->customer_phone)
                        {{ $order->customer_phone }}<br>
                    @endif
                    @if ($order->delivery_zone)
                        <span class="accent" style="font-weight:600;">{{ __('Zone:') }}</span> {{ $order->delivery_zone }}
                    @endif
                </div>
            </td>
            <td class="meta-right">
                <div class="row">
                    <span class="lbl">{{ __('Invoice #') }}</span>
                    <span style="font-weight:600;">{{ $order->order_number }}</span>
                </div>
                <div class="row">
                    <span class="lbl">{{ __('Invoice date') }}</span>
                    <span>{{ $invoiceDate->format('d-m-Y') }}</span>
                </div>
                <div class="row">
                    <span class="lbl">{{ __('Due date') }}</span>
                    <span>{{ $dueDate->format('d-m-Y') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('QTY') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Unit Price') }}</th>
                <th>{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->product_name }} — {{ $item->variant_name }}</td>
                    <td>GH₵ {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>GH₵ {{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <div class="totals-inner">
            <table>
                <tr>
                    <td>{{ __('Subtotal') }}</td>
                    <td>GH₵ {{ number_format((float) $order->subtotal, 2) }}</td>
                </tr>
                @if ((float) $order->discount_total > 0)
                    <tr>
                        <td>{{ __('Discount') }}</td>
                        <td>−GH₵ {{ number_format((float) $order->discount_total, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>{{ __('Delivery') }}</td>
                    <td>GH₵ {{ number_format((float) $order->delivery_fee, 2) }}</td>
                </tr>
                <tr class="grand">
                    <td>{{ __('Total (GHS)') }}</td>
                    <td>GH₵ {{ number_format((float) $order->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @php($pay = $order->payments->sortByDesc('id')->first())
    @if ($pay)
        <p class="payment-note">
            {{ __('Payment: :provider — :status', ['provider' => $pay->provider, 'status' => $pay->status]) }}
            @if ($pay->transaction_reference)
                — {{ __('Ref: :r', ['r' => $pay->transaction_reference]) }}
            @endif
        </p>
    @endif

    <div class="terms">
        <h3>{{ __('Terms and conditions') }}</h3>
        <p>{{ __('Payment is due within :days days of the invoice date.', ['days' => $dueDays]) }}</p>
        <p>{{ __('Please reference invoice #:num when making payment.', ['num' => $order->order_number]) }}</p>
        <p>{{ __('Questions? Contact us at :email or :phone.', ['email' => $company['email'] ?? '', 'phone' => $company['phone'] ?? '']) }}</p>
    </div>

</body>
</html>
