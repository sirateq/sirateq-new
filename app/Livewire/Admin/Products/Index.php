<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage products')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

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

    public function destroy(int $productId): void
    {
        $product = Product::query()->with('variants')->findOrFail($productId);
        $variantIds = $product->variants->modelKeys();

        if ($variantIds !== [] && DB::table('order_items')->whereIn('product_variant_id', $variantIds)->exists()) {
            Flux::toast(variant: 'danger', text: __('This product cannot be deleted because it appears on one or more orders. Disable it or archive stock instead.'));

            return;
        }

        $product->delete();

        Flux::toast(variant: 'success', text: __('Product deleted.'));

        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->with(['category', 'variants', 'images'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.products.index');
    }
}
