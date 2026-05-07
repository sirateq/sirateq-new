<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Order;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Customer')]
class Show extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user): void
    {
        abort_unless(! $user->is_admin, 404);

        $this->user = $user->loadCount('orders');
    }

    public function render()
    {
        return view('livewire.admin.customers.show', [
            'customerOrders' => Order::query()
                ->where('user_id', $this->user->id)
                ->latest()
                ->paginate(10),
        ]);
    }
}
