<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ProductImageWatermarkService;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Product form')]
class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';

    public string $description = '';

    public int $category_id = 0;

    public bool $is_active = true;

    /**
     * @var array<int, array{key: string, id: ?int, name: string, sku: string, price: float|int, quantity: int}>
     */
    public array $variants = [];

    /**
     * @var array<int, int>
     */
    public array $deletedVariantIds = [];

    /**
     * @var array<int, array{id: int, url: string}>
     */
    public array $existingImages = [];

    /**
     * @var array<int, int>
     */
    public array $deletedImageIds = [];

    public array $newImages = [];

    public string $primarySelector = '';

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $product->load(['variants.inventoryItem', 'images']);

            $this->product = $product;
            $this->name = $product->name;
            $this->description = (string) $product->description;
            $this->category_id = $product->category_id;
            $this->is_active = $product->is_active;

            $this->variants = $product->variants->map(fn (ProductVariant $variant): array => [
                'key' => (string) Str::uuid(),
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'quantity' => (int) optional($variant->inventoryItem)->quantity,
            ])->all();

            $this->existingImages = $product->images->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'url' => $image->url,
            ])->all();

            $primary = $product->images->firstWhere('is_primary', true);
            $this->primarySelector = $primary ? "existing:{$primary->id}" : '';
        }

        if (empty($this->variants)) {
            $this->addVariant();
        }
    }

    public function addVariant(): void
    {
        $this->variants[] = [
            'key' => (string) Str::uuid(),
            'id' => null,
            'name' => 'Default',
            'sku' => '',
            'price' => 0,
            'quantity' => 1000,
        ];
    }

    public function removeVariant(string $key): void
    {
        $remaining = [];
        foreach ($this->variants as $variant) {
            if ($variant['key'] === $key) {
                if ($variant['id']) {
                    $this->deletedVariantIds[] = $variant['id'];
                }

                continue;
            }
            $remaining[] = $variant;
        }
        $this->variants = array_values($remaining);
    }

    public function setPrimaryExisting(int $id): void
    {
        $this->primarySelector = "existing:{$id}";
    }

    public function setPrimaryNew(int $index): void
    {
        $this->primarySelector = "new:{$index}";
    }

    public function removeExistingImage(int $id): void
    {
        $this->deletedImageIds[] = $id;
        $this->existingImages = array_values(array_filter(
            $this->existingImages,
            fn (array $image): bool => $image['id'] !== $id,
        ));

        if ($this->primarySelector === "existing:{$id}") {
            $this->primarySelector = '';
        }
    }

    public function removeNewImage(int $index): void
    {
        if (isset($this->newImages[$index])) {
            array_splice($this->newImages, $index, 1);
        }

        if ($this->primarySelector === "new:{$index}") {
            $this->primarySelector = '';
        } elseif (str_starts_with($this->primarySelector, 'new:')) {
            $current = (int) Str::after($this->primarySelector, 'new:');
            if ($current > $index) {
                $this->primarySelector = 'new:'.($current - 1);
            }
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_active' => ['required', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.name' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.quantity' => ['required', 'integer', 'min:0'],
            'newImages.*' => ['nullable', 'image', 'max:5120'],
        ];

        foreach ($this->variants as $index => $variantRow) {
            $rules["variants.{$index}.sku"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_variants', 'sku')->ignore($variantRow['id']),
            ];
        }

        $this->validate($rules);

        DB::transaction(function (): void {
            $product = Product::query()->updateOrCreate(
                ['id' => $this->product?->id],
                [
                    'category_id' => $this->category_id,
                    'name' => $this->name,
                    'description' => $this->description ?: null,
                    'is_active' => $this->is_active,
                ],
            );

            if ($this->deletedVariantIds !== []) {
                ProductVariant::query()->whereIn('id', $this->deletedVariantIds)->delete();
            }

            foreach ($this->variants as $variantData) {
                $sku = trim((string) ($variantData['sku'] ?? ''));
                if ($sku === '') {
                    if (! empty($variantData['id'])) {
                        $existingSku = ProductVariant::query()->whereKey($variantData['id'])->value('sku');
                        $sku = (is_string($existingSku) && $existingSku !== '')
                            ? $existingSku
                            : ProductVariant::generateUniqueSku();
                    } else {
                        $sku = ProductVariant::generateUniqueSku();
                    }
                }

                $variant = ProductVariant::query()->updateOrCreate(
                    ['id' => $variantData['id']],
                    [
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'sku' => $sku,
                        'price' => $variantData['price'],
                        'is_active' => true,
                    ],
                );

                InventoryItem::query()->updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['quantity' => $variantData['quantity']],
                );
            }

            if ($this->deletedImageIds !== []) {
                $images = ProductImage::query()->whereIn('id', $this->deletedImageIds)->get();
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            $newIds = [];
            foreach ($this->newImages as $idx => $upload) {
                $path = $upload->store('products', 'public');
                ProductImageWatermarkService::applyIfEnabled(Storage::disk('public')->path($path));
                $created = ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $idx,
                ]);
                $newIds[$idx] = $created->id;
            }

            $primaryId = null;
            if (str_starts_with($this->primarySelector, 'existing:')) {
                $primaryId = (int) Str::after($this->primarySelector, 'existing:');
            } elseif (str_starts_with($this->primarySelector, 'new:')) {
                $idx = (int) Str::after($this->primarySelector, 'new:');
                $primaryId = $newIds[$idx] ?? null;
            }

            if (! $primaryId) {
                $primaryId = ProductImage::query()
                    ->where('product_id', $product->id)
                    ->orderBy('sort_order')
                    ->value('id');
            }

            ProductImage::query()
                ->where('product_id', $product->id)
                ->update(['is_primary' => false]);

            if ($primaryId) {
                ProductImage::query()->whereKey($primaryId)->update(['is_primary' => true]);
            }
        });

        Flux::toast(variant: 'success', text: __('Product saved.'));

        $this->redirectRoute('admin.products.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.products.form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
