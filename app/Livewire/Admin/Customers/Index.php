<?php

namespace App\Livewire\Admin\Customers;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Customers')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
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

    #[Computed]
    public function customers()
    {
        $allowedSort = ['name', 'email', 'created_at', 'orders_count'];
        $sortBy = in_array($this->sortBy, $allowedSort, true) ? $this->sortBy : 'created_at';
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return User::query()
            ->where('is_admin', false)
            ->withCount('orders')
            ->when($this->search !== '', function ($query): void {
                $term = $this->search;
                $query->where(function ($inner) use ($term): void {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy($sortBy, $dir)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.customers.index');
    }
}
