<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\Paystack;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.app')]
#[Title('Checkout')]
class CheckoutPage extends Component
{
    /**
     * Ghana regions and the delivery fee charged for each (in GH₵).
     */
    public const DELIVERY_ZONES = [
        'greater_accra' => ['label' => 'Greater Accra', 'fee' => 30.00],
        'ashanti' => ['label' => 'Ashanti', 'fee' => 60.00],
        'central' => ['label' => 'Central', 'fee' => 80.00],
        'eastern' => ['label' => 'Eastern', 'fee' => 80.00],
        'volta' => ['label' => 'Volta', 'fee' => 80.00],
        'western' => ['label' => 'Western', 'fee' => 80.00],
        'western_north' => ['label' => 'Western North', 'fee' => 100.00],
        'oti' => ['label' => 'Oti', 'fee' => 100.00],
        'bono' => ['label' => 'Bono', 'fee' => 100.00],
        'bono_east' => ['label' => 'Bono East', 'fee' => 100.00],
        'ahafo' => ['label' => 'Ahafo', 'fee' => 100.00],
        'northern' => ['label' => 'Northern', 'fee' => 120.00],
        'savannah' => ['label' => 'Savannah', 'fee' => 120.00],
        'north_east' => ['label' => 'North East', 'fee' => 120.00],
        'upper_east' => ['label' => 'Upper East', 'fee' => 120.00],
        'upper_west' => ['label' => 'Upper West', 'fee' => 120.00],
    ];

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $shipping_address = '';

    public ?string $delivery_zone = null;

    public string $payment_method = 'pay_now';

    public ?string $coupon_code = null;

    public ?string $appliedCoupon = null;

    public float $appliedDiscount = 0.0;

    public bool $processing = false;

    /**
     * Pay Now flow UI: form → summary (Paystack open) → verifying → failed (retry).
     * Uses explicit phase so we never flash a false "verifying" state from wire:loading.
     */
    public string $payNowFlowPhase = 'form';

    public ?string $pendingOrderNumber = null;

    public ?string $pendingPaystackReference = null;

    public ?string $verificationFailureMessage = null;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;
        }

        $stored = session()->get(CartPage::COUPON_SESSION_KEY);
        if (filled($stored)) {
            $this->coupon_code = $stored;
            $this->applyCoupon(silent: true);
        }
    }

    public function applyCoupon(bool $silent = false): void
    {
        if (blank($this->coupon_code)) {
            if (! $silent) {
                Flux::toast(variant: 'danger', text: __('Please enter a coupon code.'));
            }

            return;
        }

        $code = strtoupper(trim($this->coupon_code));

        $coupon = Coupon::query()->where('code', $code)->first();

        if (! $coupon || ! $coupon->isCurrentlyActive()) {
            $this->forgetCoupon();

            if (! $silent) {
                Flux::toast(variant: 'danger', text: __('Invalid or expired coupon code.'));
            }

            return;
        }

        $subtotal = $this->subtotal();

        if ($subtotal <= 0) {
            return;
        }

        $this->coupon_code = $code;
        $this->appliedCoupon = $code;
        $this->appliedDiscount = round(($subtotal * $coupon->discount_percentage) / 100, 2);

        session()->put(CartPage::COUPON_SESSION_KEY, $code);

        if (! $silent) {
            Flux::toast(variant: 'success', text: __('Coupon applied — you saved GH₵:amount', [
                'amount' => number_format($this->appliedDiscount, 2),
            ]));
        }
    }

    public function removeCoupon(): void
    {
        $this->forgetCoupon();
        Flux::toast(text: __('Coupon removed.'));
    }

    public function setDeliveryZone(?string $zone): void
    {
        $this->delivery_zone = $zone && array_key_exists($zone, self::DELIVERY_ZONES) ? $zone : null;
    }

    public function setPaymentMethod(string $method): void
    {
        $this->payment_method = in_array($method, ['pay_now', 'pay_on_delivery'], true) ? $method : 'pay_now';
    }

    public function placeOrder(Paystack $paystack): void
    {
        if ($this->processing) {
            return;
        }
        $this->processing = true;

        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'min:9', 'max:20'],
                'shipping_address' => ['required', 'string', 'min:5'],
                'delivery_zone' => ['required', 'string', 'in:'.implode(',', array_keys(self::DELIVERY_ZONES))],
                'payment_method' => ['required', 'in:pay_now,pay_on_delivery'],
                'coupon_code' => ['nullable', 'string', 'max:100'],
            ]);

            $cart = $this->cart;

            if (! $cart || $cart->items->isEmpty()) {
                Flux::toast(variant: 'danger', text: __('Your cart is empty.'));

                return;
            }

            if ($validated['payment_method'] === 'pay_now' && ! $paystack->isConfigured()) {
                Flux::toast(variant: 'danger', text: __('Online payment is unavailable right now. Please choose pay on delivery.'));

                return;
            }

            [$order, $payment] = DB::transaction(fn () => $this->createPendingOrder($cart, $validated, $paystack));

            if ($validated['payment_method'] === 'pay_on_delivery') {
                $this->finalizeOrder($order);
                $this->dispatch('cart-updated');
                $this->redirectRoute('shop.orders.show', ['order' => $order], navigate: true);

                return;
            }

            $this->pendingOrderNumber = $order->order_number;
            $this->pendingPaystackReference = $payment->transaction_reference;
            $this->payNowFlowPhase = 'summary';
            $this->verificationFailureMessage = null;

            $this->dispatchPaystackOpen($paystack, $order, $payment);
        } finally {
            $this->processing = false;
        }
    }

    /**
     * Triggered from JS when Paystack inline returns a successful charge.
     * The reference is then verified server-side before we finalize the order.
     */
    #[On('paystack:callback')]
    public function verifyPayment(string $reference, Paystack $paystack): void
    {
        $payment = Payment::query()
            ->with('order')
            ->where('provider', 'paystack')
            ->where('transaction_reference', $reference)
            ->latest()
            ->first();

        if (! $payment) {
            Flux::toast(variant: 'danger', text: __('Payment record not found.'));

            return;
        }

        $payment->loadMissing('order');

        $this->pendingOrderNumber = $payment->order?->order_number;
        $this->pendingPaystackReference = $reference;
        $this->payNowFlowPhase = 'verifying';
        $this->verificationFailureMessage = null;

        if (! $payment->order) {
            Flux::toast(variant: 'danger', text: __('Order not found for this payment.'));

            return;
        }

        if ($payment->status === 'paid') {
            $this->clearPayNowFlowState();
            $this->redirectRoute('shop.orders.show', ['order' => $payment->order], navigate: true);

            return;
        }

        $result = $paystack->verify($reference);
        $expectedAmount = (int) round((float) $payment->amount * 100);
        $chargedAmount = (int) ($result['data']['amount'] ?? 0);

        // Never finalize until Paystack confirms success and the charged amount matches our order total.
        if (! $result['success'] || $chargedAmount !== $expectedAmount) {
            $this->failPayment($payment, $result['message'] ?: __('Payment verification failed. Please try again.'));

            return;
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'paid']);
            // Stock, cart, and coupon redemption run only after verification above succeeds.
            $this->finalizeOrder($payment->order);
        });

        $this->clearPayNowFlowState();

        $this->dispatch('cart-updated');

        Flux::toast(variant: 'success', text: __('Payment confirmed — thank you!'));

        $this->redirectRoute('shop.orders.show', ['order' => $payment->order], navigate: true);
    }

    /**
     * Triggered from JS when the customer closes the Paystack inline modal
     * without completing payment.
     */
    #[On('paystack:cancelled')]
    public function cancelPayment(string $reference): void
    {
        $payment = Payment::query()
            ->with('order')
            ->where('provider', 'paystack')
            ->where('transaction_reference', $reference)
            ->latest()
            ->first();

        if (! $payment || $payment->status !== 'pending') {
            return;
        }

        if (! $payment->order) {
            return;
        }

        $this->pendingOrderNumber = $payment->order->order_number;
        $this->pendingPaystackReference = $reference;
        $this->payNowFlowPhase = 'summary';
        $this->verificationFailureMessage = null;

        Flux::toast(text: __('Payment window closed. Use the button below when you are ready to pay.'));
    }

    /**
     * Open Paystack again with the current pending reference (e.g. after closing the modal).
     */
    public function openPaystackCheckout(Paystack $paystack): void
    {
        if (! $paystack->isConfigured()) {
            Flux::toast(variant: 'danger', text: __('Online payment is unavailable right now.'));

            return;
        }

        $order = $this->resolvePendingPaystackOrder();
        if (! $order) {
            Flux::toast(variant: 'danger', text: __('Order not found. Please start checkout again.'));
            $this->clearPayNowFlowState();

            return;
        }

        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->where('provider', 'paystack')
            ->latest()
            ->first();

        if (! $payment || $payment->status !== 'pending') {
            Flux::toast(variant: 'danger', text: __('This order cannot be paid right now. Try “Retry payment” instead.'));

            return;
        }

        $this->pendingPaystackReference = $payment->transaction_reference;
        $this->payNowFlowPhase = 'summary';
        $this->verificationFailureMessage = null;

        $this->dispatchPaystackOpen($paystack, $order, $payment);
    }

    /**
     * After a failed verification, issue a new Paystack reference and open checkout again.
     */
    public function retryPaystackPayment(Paystack $paystack): void
    {
        if (! $paystack->isConfigured()) {
            Flux::toast(variant: 'danger', text: __('Online payment is unavailable right now.'));

            return;
        }

        $order = $this->resolvePendingPaystackOrder();
        if (! $order) {
            Flux::toast(variant: 'danger', text: __('Order not found. Please start checkout again.'));
            $this->clearPayNowFlowState();

            return;
        }

        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->where('provider', 'paystack')
            ->latest()
            ->first();

        if (! $payment || $payment->status !== 'failed') {
            Flux::toast(variant: 'danger', text: __('No payable transaction found for this order.'));

            return;
        }

        $newReference = $paystack->generateReference('SQ');
        $payment->update([
            'transaction_reference' => $newReference,
            'status' => 'pending',
        ]);

        $this->pendingPaystackReference = $newReference;
        $this->payNowFlowPhase = 'summary';
        $this->verificationFailureMessage = null;

        $this->dispatchPaystackOpen($paystack, $order, $payment->fresh());
    }

    /**
     * Drop the pending Pay Now order and return to the checkout form (cart is unchanged until payment succeeds).
     */
    public function abandonPendingPaystackOrder(): void
    {
        $order = $this->resolvePendingPaystackOrder();
        if ($order) {
            DB::transaction(function () use ($order) {
                $order->items()->delete();
                $order->payments()->delete();
                $order->delete();
            });
        }

        $this->clearPayNowFlowState();

        Flux::toast(text: __('Checkout restarted — your cart is unchanged.'));
    }

    public function subtotal(): float
    {
        return (float) ($this->cart?->subtotal() ?? 0);
    }

    public function deliveryFee(): float
    {
        if (! $this->delivery_zone || ! array_key_exists($this->delivery_zone, self::DELIVERY_ZONES)) {
            return 0.0;
        }

        return (float) self::DELIVERY_ZONES[$this->delivery_zone]['fee'];
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - $this->appliedDiscount) + $this->deliveryFee();
    }

    #[Computed]
    public function cart(): ?Cart
    {
        return Cart::query()
            ->where('status', 'active')
            ->where('session_id', session()->getId())
            ->where(fn ($query) => $query
                ->where('user_id', Auth::id())
                ->orWhereNull('user_id'))
            ->with('items.variant.product.images')
            ->latest()
            ->first();
    }

    /**
     * Order created for Pay Now, awaiting verification (used in summary UI).
     */
    #[Computed]
    public function pendingPaystackOrder(): ?Order
    {
        if ($this->payNowFlowPhase === 'form' || blank($this->pendingOrderNumber)) {
            return null;
        }

        return Order::query()
            ->where('order_number', $this->pendingOrderNumber)
            ->with(['items', 'payments'])
            ->first();
    }

    protected function resolvePendingPaystackOrder(): ?Order
    {
        if (blank($this->pendingOrderNumber)) {
            return null;
        }

        return Order::query()
            ->where('order_number', $this->pendingOrderNumber)
            ->where('status', 'pending_payment')
            ->first();
    }

    protected function dispatchPaystackOpen(Paystack $paystack, Order $order, Payment $payment): void
    {
        $this->dispatch(
            'paystack:open',
            publicKey: $paystack->publicKey(),
            email: $order->customer_email,
            amount: (int) round((float) $order->total * 100),
            reference: $payment->transaction_reference,
            currency: $paystack->currency(),
            metadata: [
                'order_number' => (string) $order->order_number,
                'phone' => $order->customer_phone,
                'custom_fields' => [
                    ['display_name' => 'Order', 'variable_name' => 'order_number', 'value' => $order->order_number],
                    ['display_name' => 'Phone', 'variable_name' => 'phone', 'value' => $order->customer_phone],
                ],
            ],
        );
    }

    protected function clearPayNowFlowState(): void
    {
        $this->payNowFlowPhase = 'form';
        $this->pendingOrderNumber = null;
        $this->pendingPaystackReference = null;
        $this->verificationFailureMessage = null;
    }

    /**
     * Create the order, items, and a pending payment record. Stock and cart
     * are NOT touched here — those side-effects happen on `finalizeOrder()`
     * once payment is confirmed (or immediately for pay-on-delivery).
     *
     * @param  array<string, mixed>  $validated
     * @return array{0: Order, 1: Payment}
     */
    protected function createPendingOrder(Cart $cart, array $validated, Paystack $paystack): array
    {
        $subtotal = (float) $cart->subtotal();
        $deliveryFee = (float) (self::DELIVERY_ZONES[$validated['delivery_zone']]['fee'] ?? 0);
        $discountTotal = 0.0;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::query()->where('code', strtoupper($validated['coupon_code']))->first();
            if ($coupon && $coupon->isCurrentlyActive()) {
                $discountTotal = round(($subtotal * $coupon->discount_percentage) / 100, 2);
            }
        }

        $total = max(0, $subtotal - $discountTotal) + $deliveryFee;

        $order = Order::query()->create([
            'user_id' => Auth::id(),
            'order_number' => $this->generateUniqueSixDigitOrderNumber(),
            'status' => $validated['payment_method'] === 'pay_on_delivery' ? 'placed' : 'pending_payment',
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'shipping_address' => $validated['shipping_address'],
            'delivery_zone' => self::DELIVERY_ZONES[$validated['delivery_zone']]['label'],
            'payment_method' => $validated['payment_method'],
        ]);

        foreach ($cart->items as $item) {
            $variant = ProductVariant::query()->with('product')->findOrFail($item->product_variant_id);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_name' => $variant->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => (float) $item->unit_price * $item->quantity,
            ]);
        }

        $reference = $validated['payment_method'] === 'pay_now'
            ? $paystack->generateReference('SQ')
            : 'COD-'.strtoupper((string) str()->random(10));

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'provider' => $validated['payment_method'] === 'pay_now' ? 'paystack' : 'cash_on_delivery',
            'status' => 'pending',
            'amount' => $total,
            'transaction_reference' => $reference,
        ]);

        return [$order->fresh('items'), $payment];
    }

    /**
     * Decrement stock, record coupon redemption, clear the cart, and mark
     * the order as placed. Safe to call once per order.
     */
    protected function finalizeOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $variant = ProductVariant::query()->with('inventoryItem')->find($item->product_variant_id);
                $variant?->inventoryItem?->decrement('quantity', $item->quantity);
            }

            if ((float) $order->discount_total > 0 && $this->appliedCoupon) {
                $coupon = Coupon::query()->where('code', $this->appliedCoupon)->first();
                if ($coupon) {
                    $coupon->increment('used_count');
                    $coupon->redemptions()->create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                    ]);
                }
            }

            $cart = $this->cart;
            if ($cart) {
                $cart->items()->delete();
                $cart->update(['status' => 'converted']);
            }

            session()->forget(CartPage::COUPON_SESSION_KEY);

            if ($order->status === 'pending_payment') {
                $order->update(['status' => 'placed']);
            }
        });
    }

    protected function failPayment(Payment $payment, string $message): void
    {
        $payment->loadMissing('order');
        $payment->update(['status' => 'failed']);
        // Keep order pending_payment so the customer can retry with a new reference.

        $this->pendingOrderNumber = $payment->order?->order_number;
        $this->pendingPaystackReference = $payment->transaction_reference;
        $this->payNowFlowPhase = 'failed';
        $this->verificationFailureMessage = $message;

        Flux::toast(variant: 'danger', text: $message);
    }

    protected function forgetCoupon(): void
    {
        $this->coupon_code = null;
        $this->appliedCoupon = null;
        $this->appliedDiscount = 0.0;

        session()->forget(CartPage::COUPON_SESSION_KEY);
    }

    /**
     * Human-readable order identifier: exactly 6 digits, zero-padded, globally unique in `orders.order_number`.
     */
    protected function generateUniqueSixDigitOrderNumber(): string
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $candidate = str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);

            if (! Order::query()->where('order_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Unable to generate a unique 6-digit order number after 100 attempts.');
    }

    public function render()
    {
        return view('livewire.shop.checkout-page');
    }
}
