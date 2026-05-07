<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Categories trash')]
class Trash extends Component
{
    use WithPagination;

    public string $sortBy = 'deleted_at';

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

    public function restore(int $categoryId): void
    {
        Category::onlyTrashed()->findOrFail($categoryId)->restore();
    }

    public function destroy(int $categoryId): void
    {
        Category::onlyTrashed()->findOrFail($categoryId)->forceDelete();
    }

    #[Computed]
    public function trashedCategories()
    {
        return Category::onlyTrashed()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.categories.trash');
    }
}
