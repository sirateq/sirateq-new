<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order confirmation')]
class OrderConfirmation extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items', 'payments']);
    }

    public function render()
    {
        return view('livewire.shop.order-confirmation');
    }
}
