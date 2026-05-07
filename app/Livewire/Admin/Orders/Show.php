<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order details')]
class Show extends Component
{
    public Order $order;

    public string $status = 'pending';

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items', 'payments']);
        $this->status = $order->status;
    }

    public function updateStatus(): void
    {
        $validated = $this->validate([
            'status' => ['required', 'in:pending,placed,paid,shipped,cancelled'],
        ]);

        $this->order->update(['status' => $validated['status']]);

        Log::info('Admin order status updated', [
            'admin_user_id' => auth()->id(),
            'order_id' => $this->order->id,
            'status' => $validated['status'],
        ]);
    }

    public function render()
    {
        return view('livewire.admin.orders.show');
    }
}
