<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Category form')]
class Form extends Component
{
    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public bool $is_active = true;

    public function mount(?Category $category = null): void
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = (string) $category->description;
            $this->is_active = $category->is_active;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.($this->category?->id ?? 'NULL')],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category = Category::query()->updateOrCreate(
            ['id' => $this->category?->id],
            $validated,
        );

        $this->redirectRoute('admin.categories.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.categories.form');
    }
}
