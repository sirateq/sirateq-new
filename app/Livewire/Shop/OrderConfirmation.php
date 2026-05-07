<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Paystack;
use App\Services\PaystackPaymentCompletionService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Order confirmation')]
class OrderConfirmation extends Component
{
    public Order $order;

    public bool $verifying = false;

    public ?string $verificationFailureMessage = null;

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items', 'payments']);

        if (! $this->order->isAccessibleByCurrentCustomer()) {
            abort(404);
        }
    }

    #[Computed]
    public function latestPaystackPayment(): ?Payment
    {
        if ($this->order->payment_method !== 'pay_now') {
            return null;
        }

        return Payment::query()
            ->where('order_id', $this->order->id)
            ->where('provider', 'paystack')
            ->latest()
            ->first();
    }

    public function requiresPaystackVerification(): bool
    {
        if ($this->order->status !== 'pending_payment' || $this->order->payment_method !== 'pay_now') {
            return false;
        }

        $payment = $this->latestPaystackPayment;

        return $payment !== null && in_array($payment->status, ['pending', 'failed'], true);
    }

    public function openPaystackCheckout(Paystack $paystack): void
    {
        if (! $paystack->isConfigured()) {
            Flux::toast(variant: 'danger', text: __('Online payment is unavailable right now.'));

            return;
        }

        $this->order->refresh();

        if ($this->order->status !== 'pending_payment') {
            Flux::toast(variant: 'warning', text: __('This order is already finalized.'));

            return;
        }

        $payment = Payment::query()
            ->where('order_id', $this->order->id)
            ->where('provider', 'paystack')
            ->latest()
            ->first();

        if (! $payment || $payment->status !== 'pending') {
            Flux::toast(variant: 'danger', text: __('This order cannot be paid right now. Try “Retry payment” if payment failed.'));

            return;
        }

        $this->verificationFailureMessage = null;

        $this->dispatchPaystackOpen($paystack, $this->order->fresh(['items']), $payment);
    }

    public function retryPaystackPayment(Paystack $paystack): void
    {
        if (! $paystack->isConfigured()) {
            Flux::toast(variant: 'danger', text: __('Online payment is unavailable right now.'));

            return;
        }

        $this->order->refresh();

        if ($this->order->status !== 'pending_payment') {
            Flux::toast(variant: 'warning', text: __('This order is already finalized.'));

            return;
        }

        $payment = Payment::query()
            ->where('order_id', $this->order->id)
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

        $this->verificationFailureMessage = null;

        $this->dispatchPaystackOpen($paystack, $this->order->fresh(['items']), $payment->fresh());
    }

    #[On('paystack:callback')]
    public function verifyPayment(string $reference, PaystackPaymentCompletionService $completion): void
    {
        $this->verifying = true;
        $this->verificationFailureMessage = null;

        $result = $completion->verifyReferenceAndFulfillOrder($reference);

        $this->verifying = false;

        if (! $result['payment']) {
            Flux::toast(variant: 'danger', text: $result['message'] ?? __('Payment record not found.'));

            return;
        }

        if (! $result['order'] || $result['order']->id !== $this->order->id) {
            Flux::toast(variant: 'danger', text: __('This payment does not belong to this order.'));

            return;
        }

        if ($result['success']) {
            $this->order->refresh()->load(['items', 'payments']);
            Flux::toast(variant: 'success', text: __('Payment confirmed — thank you!'));

            return;
        }

        $this->verificationFailureMessage = $result['message'] ?: __('Payment verification failed. Please try again.');
        $this->order->refresh()->load(['items', 'payments']);
        Flux::toast(variant: 'danger', text: $this->verificationFailureMessage);
    }

    public function verifyLatestPaystackPayment(PaystackPaymentCompletionService $completion): void
    {
        $payment = $this->latestPaystackPayment;
        if (! $payment || ! in_array($payment->status, ['pending', 'failed'], true)) {
            Flux::toast(variant: 'danger', text: __('Nothing to verify for this order.'));

            return;
        }

        $this->verifyPayment($payment->transaction_reference, $completion);
    }

    #[On('paystack:cancelled')]
    public function onPaystackCancelled(?string $reference = null): void
    {
        Flux::toast(text: __('Payment window closed. Use “Open Paystack” when you are ready to pay.'));
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

    public function render()
    {
        return view('livewire.shop.order-confirmation');
    }
}
