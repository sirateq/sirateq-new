<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

#[Fillable([
    'user_id',
    'coupon_id',
    'order_number',
    'status',
    'subtotal',
    'discount_total',
    'delivery_fee',
    'total',
    'customer_name',
    'customer_email',
    'customer_phone',
    'shipping_address',
    'delivery_zone',
    'payment_method',
])]
class Order extends Model
{
    use HasFactory;

    /**
     * Guest customers can open order confirmation and invoices in this browser session
     * after checkout stores the id here (see {@see self::grantCustomerSessionAccess}).
     */
    public const CUSTOMER_SESSION_ORDER_IDS_KEY = 'shop_order_access_ids';

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static function grantCustomerSessionAccess(self $order): void
    {
        $ids = session()->get(self::CUSTOMER_SESSION_ORDER_IDS_KEY, []);
        $ids[] = $order->id;
        $ids = array_values(array_unique($ids));
        if (count($ids) > 30) {
            $ids = array_slice($ids, -30);
        }
        session()->put(self::CUSTOMER_SESSION_ORDER_IDS_KEY, $ids);
    }

    public function isAccessibleByCurrentCustomer(): bool
    {
        $user = Auth::user();
        if ($user !== null && $this->user_id !== null && (int) $this->user_id === (int) $user->getAuthIdentifier()) {
            return true;
        }

        $ids = session()->get(self::CUSTOMER_SESSION_ORDER_IDS_KEY, []);

        return in_array($this->id, $ids, true);
    }

    /**
     * Time-limited URL for guests to open the storefront order page (e.g. from email / SMS).
     */
    public function temporarySignedStorefrontUrl(): string
    {
        return URL::temporarySignedRoute(
            'shop.orders.signed-show',
            now()->addDays(config('shop_notifications.signed_url_expiry_days')),
            ['order' => $this],
        );
    }

    /**
     * Time-limited URL for guests to download the invoice PDF.
     */
    public function temporarySignedInvoiceDownloadUrl(): string
    {
        return URL::temporarySignedRoute(
            'shop.orders.signed-invoice',
            now()->addDays(config('shop_notifications.signed_url_expiry_days')),
            ['order' => $this],
        );
    }

    /**
     * Timeline steps for storefront order tracking UI.
     *
     * @return list<array{key: string, label: string, description: string, state: 'complete'|'current'|'upcoming', date: ?Carbon}>
     */
    public function trackingTimeline(): array
    {
        $this->loadMissing('payments');

        if ($this->status === 'cancelled') {
            return [
                [
                    'key' => 'cancelled',
                    'label' => __('Order cancelled'),
                    'description' => __('This order was cancelled. Contact us if you have questions.'),
                    'state' => 'current',
                    'date' => $this->updated_at,
                ],
            ];
        }

        $paymentComplete = $this->status !== 'pending_payment';
        $paymentDate = $this->payments->firstWhere('status', 'paid')?->updated_at;
        $shippedComplete = $this->status === 'shipped';

        $steps = [
            [
                'key' => 'received',
                'label' => __('Order received'),
                'description' => __('We received your order and are processing it.'),
                'state' => 'complete',
                'date' => $this->created_at,
            ],
            [
                'key' => 'payment',
                'label' => $this->payment_method === 'pay_on_delivery'
                    ? __('Pay on delivery')
                    : __('Payment'),
                'description' => $this->payment_method === 'pay_on_delivery'
                    ? __('Payment will be collected when your order arrives.')
                    : __('Secure online payment for this order.'),
                'state' => $paymentComplete ? 'complete' : 'current',
                'date' => $paymentComplete ? ($paymentDate ?? $this->updated_at) : null,
            ],
            [
                'key' => 'preparing',
                'label' => __('Preparing your order'),
                'description' => __('We are packing and quality-checking your items.'),
                'state' => 'upcoming',
                'date' => null,
            ],
            [
                'key' => 'shipped',
                'label' => __('Shipped'),
                'description' => __('Your order is on its way to you.'),
                'state' => 'upcoming',
                'date' => null,
            ],
        ];

        if (! $paymentComplete) {
            return $steps;
        }

        if ($shippedComplete) {
            $steps[2]['state'] = 'complete';
            $steps[2]['date'] = $this->updated_at;
            $steps[3]['state'] = 'complete';
            $steps[3]['date'] = $this->updated_at;

            return $steps;
        }

        $steps[2]['state'] = 'current';
        $steps[2]['date'] = $this->updated_at;
        $steps[3]['state'] = 'upcoming';

        return $steps;
    }
}
