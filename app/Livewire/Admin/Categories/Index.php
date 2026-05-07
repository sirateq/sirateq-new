<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage categories')]
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

    public function trash(int $categoryId): void
    {
        Category::query()->findOrFail($categoryId)->delete();
    }

    public function restore(int $categoryId): void
    {
        Category::onlyTrashed()->findOrFail($categoryId)->restore();
    }

    public function destroy(int $categoryId): void
    {
        Category::onlyTrashed()->findOrFail($categoryId)->forceDelete();
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function trashedCategories()
    {
        return Category::onlyTrashed()->latest('deleted_at')->paginate(10, ['*'], 'trashedPage');
    }

    public function render()
    {
        return view('livewire.admin.categories.index');
    }
}
