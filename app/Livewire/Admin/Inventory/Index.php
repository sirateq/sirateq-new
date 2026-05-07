<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Inventory')]
class Index extends Component
{
    use WithPagination;

    public string $sortBy = 'quantity';

    public string $sortDirection = 'asc';

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
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.inventory.index');
    }
}
