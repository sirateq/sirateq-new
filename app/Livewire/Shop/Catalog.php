<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Shop')]
class Catalog extends Component
{
    public function render()
    {
        return view('livewire.shop.catalog', [
            'products' => Product::query()
                ->where('is_active', true)
                ->with(['category', 'variants'])
                ->latest()
                ->get(),
        ]);
    }
}
