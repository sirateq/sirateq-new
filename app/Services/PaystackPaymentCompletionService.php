<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaystackPaymentCompletionService
{
    public function __construct(
        private Paystack $paystack,
        private OrderFulfillmentService $fulfillment,
    ) {}

    /**
     * @return array{success: bool, message: ?string, order: ?Order, payment: ?Payment}
     */
    public function verifyReferenceAndFulfillOrder(string $reference): array
    {
        $payment = Payment::query()
            ->with('order')
            ->where('provider', 'paystack')
            ->where('transaction_reference', $reference)
            ->latest()
            ->first();

        if (! $payment) {
            return [
                'success' => false,
                'message' => __('Payment record not found.'),
                'order' => null,
                'payment' => null,
            ];
        }

        $payment->loadMissing('order');

        if (! $payment->order) {
            return [
                'success' => false,
                'message' => __('Order not found for this payment.'),
                'order' => null,
                'payment' => $payment,
            ];
        }

        if ($payment->status === 'paid') {
            return [
                'success' => true,
                'message' => null,
                'order' => $payment->order,
                'payment' => $payment,
            ];
        }

        $result = $this->paystack->verify($reference);
        $expectedAmount = (int) round((float) $payment->amount * 100);
        $chargedAmount = (int) ($result['data']['amount'] ?? 0);

        if (! $result['success'] || $chargedAmount !== $expectedAmount) {
            $payment->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => $result['message'] ?: __('Payment verification failed. Please try again.'),
                'order' => $payment->order,
                'payment' => $payment->fresh(),
            ];
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'paid']);
            $this->fulfillment->finalizePaidOrder($payment->order->fresh());
        });

        return [
            'success' => true,
            'message' => null,
            'order' => $payment->order->fresh(),
            'payment' => $payment->fresh(),
        ];
    }
}
