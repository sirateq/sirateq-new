<?php

namespace App\Livewire\Admin\Discounts;

use App\Models\Coupon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Discounts')]
class Index extends Component
{
    use WithPagination;

    public string $code = '';

    public string $name = '';

    public int $discount_percentage = 10;

    #[Url(as: 'q')]
    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $couponId): void
    {
        $coupon = Coupon::query()->findOrFail($couponId);
        $coupon->update(['is_active' => ! $coupon->is_active]);
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

    public function save(): void
    {
        $validated = $this->validate([
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'discount_percentage' => ['required', 'integer', 'between:1,95'],
        ]);

        $coupon = Coupon::query()->updateOrCreate(
            ['code' => strtoupper($validated['code'])],
            [
                'name' => $validated['name'],
                'discount_percentage' => $validated['discount_percentage'],
                'is_active' => true,
            ],
        );

        Log::info('Admin coupon upserted', [
            'admin_user_id' => auth()->id(),
            'coupon_id' => $coupon->id,
        ]);

        $this->reset(['code', 'name', 'discount_percentage']);
        $this->discount_percentage = 10;
    }

    #[Computed]
    public function coupons()
    {
        return Coupon::query()
            ->when($this->search !== '', function ($query) {
                $query->where('code', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.discounts.index');
    }
}
