<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage products')]
class Index extends Component
{
    use WithPagination;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        $product->update(['is_active' => ! $product->is_active]);
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->with(['category', 'variants'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.products.index');
    }
}
