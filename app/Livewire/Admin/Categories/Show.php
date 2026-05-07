<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Category details')]
class Show extends Component
{
    public Category $category;

    public function mount(int $categoryId): void
    {
        $this->category = Category::withTrashed()->findOrFail($categoryId);
    }

    public function render()
    {
        return view('livewire.admin.categories.show');
    }
}
