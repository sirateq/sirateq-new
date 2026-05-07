<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Shop\SendOrderPlacedNotifications;
use App\Mail\AdminCustomCustomerNoticeMail;
use App\Models\Order;
use App\Services\SmsDeliveryService;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order details')]
class Show extends Component
{
    public Order $order;

    public string $status = 'pending';

    public string $customEmailSubject = '';

    public string $customEmailBody = '';

    public string $customSmsBody = '';

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items', 'payments', 'coupon']);
        $this->status = $order->status;
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            'pending_payment' => __('Awaiting payment'),
            'pending' => __('Pending'),
            'placed' => __('Placed'),
            'paid' => __('Paid'),
            'shipped' => __('Shipped'),
            'cancelled' => __('Cancelled'),
        ];
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'paid', 'shipped' => 'green',
            'placed' => 'blue',
            'pending_payment', 'pending' => 'amber',
            'cancelled' => 'red',
            default => 'zinc',
        };
    }

    #[Computed]
    public function customerStorefrontUrl(): string
    {
        return $this->order->temporarySignedStorefrontUrl();
    }

    public function updateStatus(): void
    {
        $validated = $this->validate([
            'status' => ['required', 'in:pending,pending_payment,placed,paid,shipped,cancelled'],
        ]);

        $this->order->update(['status' => $validated['status']]);
        $this->order->refresh();
        $this->order->load(['items', 'payments', 'coupon']);

        Log::info('Admin order status updated', [
            'admin_user_id' => auth()->id(),
            'order_id' => $this->order->id,
            'status' => $validated['status'],
        ]);

        Flux::toast(variant: 'success', text: __('Order status updated.'));
    }

    public function sendCustomCustomerEmail(): void
    {
        $validated = $this->validate([
            'customEmailSubject' => ['required', 'string', 'max:200'],
            'customEmailBody' => ['required', 'string', 'max:20000'],
        ]);

        Mail::to($this->order->customer_email)->send(new AdminCustomCustomerNoticeMail(
            $this->order,
            $validated['customEmailSubject'],
            $validated['customEmailBody'],
        ));

        Log::info('Admin sent custom email to customer', [
            'admin_user_id' => auth()->id(),
            'order_id' => $this->order->id,
        ]);

        $this->reset('customEmailSubject', 'customEmailBody');
        Flux::toast(variant: 'success', text: __('Email sent to the customer.'));
    }

    public function sendCustomCustomerSms(): void
    {
        if (! filled($this->order->customer_phone)) {
            Flux::toast(variant: 'danger', text: __('This order has no phone number on file.'));

            return;
        }

        $validated = $this->validate([
            'customSmsBody' => ['required', 'string', 'max:1000'],
        ]);

        app(SmsDeliveryService::class)->send($this->order->customer_phone, $validated['customSmsBody']);

        Log::info('Admin sent custom SMS to customer', [
            'admin_user_id' => auth()->id(),
            'order_id' => $this->order->id,
        ]);

        $this->reset('customSmsBody');
        Flux::toast(variant: 'success', text: __('SMS sent to the customer.'));
    }

    public function resendCustomerOrderNotice(): void
    {
        app(SendOrderPlacedNotifications::class)->sendCustomerNoticeOnly(
            $this->order->fresh(['items', 'payments', 'coupon'])
        );

        Log::info('Admin resent standard order notice to customer', [
            'admin_user_id' => auth()->id(),
            'order_id' => $this->order->id,
        ]);

        Flux::toast(variant: 'success', text: __('Standard order email and SMS were resent to the customer (if phone is on file).'));
    }

    public function render()
    {
        return view('livewire.admin.orders.show');
    }
}
