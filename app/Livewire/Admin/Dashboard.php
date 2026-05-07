<?php

namespace App\Livewire\Admin;

use App\Models\InventoryItem;
use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('dashboard', [
            'ordersToday' => Order::query()->whereDate('created_at', today())->count(),
            'revenueToday' => (float) Order::query()->whereDate('created_at', today())->sum('total'),
            'lowStockCount' => InventoryItem::query()->whereColumn('quantity', '<=', 'low_stock_threshold')->count(),
            'recentOrders' => Order::query()->latest()->limit(5)->get(),
        ]);
    }
}
