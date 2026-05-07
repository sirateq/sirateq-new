<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Admin users')]
class Index extends Component
{
    use WithPagination;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

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

    public function saveAdmin(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        Log::info('Admin user created', [
            'created_by' => auth()->id(),
            'new_admin_id' => $user->id,
        ]);

        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        Flux::toast(variant: 'success', text: __('Admin user created. They can sign in with the password you set.'));
    }

    public function revokeAdmin(int $userId): void
    {
        $target = User::query()->where('is_admin', true)->findOrFail($userId);

        if ($target->id === auth()->id()) {
            Flux::toast(variant: 'danger', text: __('You cannot remove your own admin access.'));

            return;
        }

        if (User::query()->where('is_admin', true)->count() <= 1) {
            Flux::toast(variant: 'danger', text: __('You must keep at least one admin.'));

            return;
        }

        $target->update(['is_admin' => false]);

        Log::info('Admin access revoked', [
            'revoked_by' => auth()->id(),
            'target_user_id' => $userId,
        ]);

        Flux::toast(variant: 'success', text: __('Admin access removed from this user.'));
    }

    #[Computed]
    public function admins()
    {
        $allowedSort = ['name', 'email', 'created_at'];
        $sortBy = in_array($this->sortBy, $allowedSort, true) ? $this->sortBy : 'created_at';
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return User::query()
            ->where('is_admin', true)
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
        return view('livewire.admin.users.index');
    }
}
