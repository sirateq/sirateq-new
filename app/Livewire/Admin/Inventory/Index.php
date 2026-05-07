<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Inventory')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $stockFilter = '';

    public string $sortBy = 'quantity';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
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

    public function adjust(int $inventoryId, int $delta): void
    {
        $item = InventoryItem::query()->findOrFail($inventoryId);
        $item->update(['quantity' => max(0, $item->quantity + $delta)]);

        Log::info('Admin inventory adjusted', [
            'admin_user_id' => auth()->id(),
            'inventory_item_id' => $item->id,
            'delta' => $delta,
            'new_quantity' => $item->quantity,
        ]);
    }

    #[Computed]
    public function items()
    {
        return InventoryItem::query()
            ->with('variant.product')
            ->when($this->search !== '', function ($query) {
                $query->whereHas('variant', function ($v) {
                    $v->where('sku', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->stockFilter === 'low', fn ($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold'))
            ->when($this->stockFilter === 'out', fn ($q) => $q->where('quantity', 0))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.inventory.index');
    }
}
