<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Track your order')]
class OrderTracking extends Component
{
    public string $order_number = '';

    public string $email = '';

    public function lookup()
    {
        $validated = $this->validate([
            'order_number' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $number = ltrim(trim($validated['order_number']), '#');
        $email = strtolower(trim($validated['email']));

        $order = Order::query()
            ->where('order_number', $number)
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->first();

        if (! $order) {
            Flux::toast(variant: 'danger', text: __('We could not find an order with that number and email. Check your details and try again.'));

            return;
        }

        Order::grantCustomerSessionAccess($order);

        return $this->redirect(route('shop.orders.show', $order), navigate: false);
    }

    public function render()
    {
        return view('livewire.shop.order-tracking');
    }
}
