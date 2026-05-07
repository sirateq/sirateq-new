<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product form')]
class Form extends Component
{
    public ?Product $product = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public int $category_id = 0;

    public string $variant_name = 'Default';

    public string $sku = '';

    public float $price = 0;

    public int $quantity = 0;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $product->load('variants.inventoryItem');
            $variant = $product->variants->first();

            $this->product = $product;
            $this->name = $product->name;
            $this->description = (string) $product->description;
            $this->category_id = $product->category_id;
            $this->variant_name = (string) $variant?->name;
            $this->sku = (string) $variant?->sku;
            $this->price = (float) $variant?->price;
            $this->quantity = (int) optional($variant?->inventoryItem)->quantity;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'variant_name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::query()->updateOrCreate(
            ['id' => $this->product?->id],
            [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'is_active' => true,
            ],
        );

        $variant = ProductVariant::query()->updateOrCreate(
            ['product_id' => $product->id, 'sku' => $validated['sku']],
            [
                'name' => $validated['variant_name'],
                'price' => $validated['price'],
                'is_active' => true,
            ],
        );

        InventoryItem::query()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            ['quantity' => $validated['quantity']],
        );

        $this->redirectRoute('admin.products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.products.form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
