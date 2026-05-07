<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $search = '',
        private readonly string $status = '',
        private readonly string $sortBy = 'created_at',
        private readonly string $sortDirection = 'desc',
    ) {}

    public function query(): Builder
    {
        $sortBy = $this->normalizedSortBy();
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Order::query()
            ->with(['items', 'coupon'])
            ->when($this->search !== '', function (Builder $query): void {
                $term = $this->search;
                $query->where(function (Builder $q) use ($term): void {
                    $q->where('order_number', 'like', "%{$term}%")
                        ->orWhere('customer_email', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%");
                });
            })
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->orderBy($sortBy, $sortDirection);
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Order number',
            'Status',
            'Placed at',
            'Customer name',
            'Customer email',
            'Customer phone',
            'Shipping address',
            'Delivery zone',
            'Delivery fee',
            'Payment method',
            'Subtotal',
            'Discount total',
            'Total',
            'Coupon code',
            'Line items',
        ];
    }

    /**
     * @param  Order  $order
     * @return list<string|float|null>
     */
    public function map($order): array
    {
        $lineItems = $order->items
            ->map(fn ($item) => "{$item->product_name} ({$item->variant_name}) × {$item->quantity} @ {$item->unit_price}")
            ->implode(' | ');

        return [
            $order->order_number,
            $order->status,
            $order->created_at?->format('Y-m-d H:i:s'),
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->shipping_address,
            $order->delivery_zone,
            (float) $order->delivery_fee,
            $order->payment_method,
            (float) $order->subtotal,
            (float) $order->discount_total,
            (float) $order->total,
            $order->coupon?->code ?? '',
            $lineItems,
        ];
    }

    private function normalizedSortBy(): string
    {
        $allowed = ['order_number', 'customer_email', 'created_at', 'status', 'total'];

        return in_array($this->sortBy, $allowed, true) ? $this->sortBy : 'created_at';
    }
}
