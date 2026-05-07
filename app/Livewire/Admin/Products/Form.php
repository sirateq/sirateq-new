<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOptionGroup;
use App\Models\ProductOptionStructure;
use App\Models\ProductVariant;
use App\Models\ProductVariantOptionSelection;
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
     * Each group: name, display_type, values[{label, hex_color, product_image_id?}]. IDs added during edit after load.
     *
     * @var array<int, array{id: ?int, name: string, display_type: string, values: array<int, array{id: ?int, label: string, hex_color: string, product_image_id: ?int}>}>
     */
    public array $optionGroups = [];

    /**
     * @var array<int, array{key: string, id: ?int, sku: string, price: float|int, quantity: int, selected_value_indexes: array<int, int>}>
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
            $product->load([
                'variants.inventoryItem',
                'variants.optionSelections',
                'optionGroups.values',
                'images',
            ]);

            $this->product = $product;
            $this->name = $product->name;
            $this->description = (string) $product->description;
            $this->category_id = $product->category_id;
            $this->is_active = $product->is_active;

            $orderedGroups = $product->optionGroups;

            foreach ($orderedGroups as $group) {
                $valueRows = [];
                foreach ($group->values as $value) {
                    $valueRows[] = [
                        'id' => $value->id,
                        'label' => $value->label,
                        'hex_color' => $value->hex_color ?? '',
                        'product_image_id' => $value->product_image_id,
                    ];
                }
                if ($valueRows === []) {
                    $valueRows[] = ['id' => null, 'label' => __('Default'), 'hex_color' => '', 'product_image_id' => null];
                }
                $this->optionGroups[] = [
                    'id' => $group->id,
                    'name' => $group->name,
                    'display_type' => $group->display_type,
                    'values' => $valueRows,
                ];
            }

            if ($this->optionGroups === []) {
                $this->seedDefaultOptionStructure();
            }

            foreach ($product->variants as $variant) {
                $indexes = [];
                if ($orderedGroups->isNotEmpty()) {
                    foreach ($orderedGroups->values() as $gi => $group) {
                        $selection = $variant->optionSelections->firstWhere('product_option_group_id', $group->id);
                        $valueId = $selection?->product_option_value_id;
                        $vals = $group->values;
                        $idx = $vals->search(fn ($v) => $v->id === $valueId);
                        $indexes[$gi] = $idx !== false ? (int) $idx : 0;
                    }
                } else {
                    foreach (array_keys($this->optionGroups) as $gi) {
                        $indexes[$gi] = 0;
                    }
                }
                $this->variants[] = [
                    'key' => (string) Str::uuid(),
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => (float) $variant->price,
                    'quantity' => (int) optional($variant->inventoryItem)->quantity,
                    'selected_value_indexes' => $indexes,
                ];
            }

            $this->existingImages = $product->images->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'url' => $image->url,
            ])->all();

            $primary = $product->images->firstWhere('is_primary', true);
            $this->primarySelector = $primary ? "existing:{$primary->id}" : '';
        }

        if ($this->optionGroups === []) {
            $this->seedDefaultOptionStructure();
        }

        if ($this->variants === []) {
            $this->addVariant();
        }
    }

    protected function seedDefaultOptionStructure(): void
    {
        $this->optionGroups = [[
            'id' => null,
            'name' => __('Option'),
            'display_type' => ProductOptionGroup::DISPLAY_TEXT,
            'values' => [[
                'id' => null,
                'label' => __('Default'),
                'hex_color' => '',
                'product_image_id' => null,
            ]],
        ]];
    }

    public function addOptionGroup(): void
    {
        $this->optionGroups[] = [
            'id' => null,
            'name' => __('New option'),
            'display_type' => ProductOptionGroup::DISPLAY_TEXT,
            'values' => [[
                'id' => null,
                'label' => __('Value 1'),
                'hex_color' => '',
                'product_image_id' => null,
            ]],
        ];
        $gi = count($this->optionGroups) - 1;
        foreach ($this->variants as $i => $variant) {
            $this->variants[$i]['selected_value_indexes'][$gi] = 0;
        }
    }

    public function removeOptionGroup(int $index): void
    {
        if (count($this->optionGroups) <= 1) {
            Flux::toast(variant: 'danger', text: __('Keep at least one option (e.g. Color or Size).'));

            return;
        }

        if (! isset($this->optionGroups[$index])) {
            return;
        }

        array_splice($this->optionGroups, $index, 1);

        foreach ($this->variants as $i => $variant) {
            $old = $variant['selected_value_indexes'] ?? [];
            $newSel = [];
            foreach (array_keys($this->optionGroups) as $newGi) {
                $oldGi = $newGi < $index ? $newGi : $newGi + 1;
                $newSel[$newGi] = (int) ($old[$oldGi] ?? 0);
            }
            $this->variants[$i]['selected_value_indexes'] = $newSel;
        }
    }

    public function addOptionValue(int $groupIndex): void
    {
        if (! isset($this->optionGroups[$groupIndex])) {
            return;
        }
        $this->optionGroups[$groupIndex]['values'][] = [
            'id' => null,
            'label' => __('New value'),
            'hex_color' => '',
            'product_image_id' => null,
        ];
    }

    public function removeOptionValue(int $groupIndex, int $valueIndex): void
    {
        $group = &$this->optionGroups[$groupIndex];
        if (count($group['values']) <= 1) {
            Flux::toast(variant: 'danger', text: __('Each option needs at least one value.'));

            return;
        }
        array_splice($group['values'], $valueIndex, 1);
        foreach ($this->variants as $i => $variant) {
            $idx = $variant['selected_value_indexes'][$groupIndex] ?? 0;
            if ($idx === $valueIndex) {
                $this->variants[$i]['selected_value_indexes'][$groupIndex] = 0;
            } elseif ($idx > $valueIndex) {
                $this->variants[$i]['selected_value_indexes'][$groupIndex] = $idx - 1;
            }
        }
    }

    public function addVariant(): void
    {
        $indexes = [];
        foreach (array_keys($this->optionGroups) as $gi) {
            $indexes[$gi] = 0;
        }
        $this->variants[] = [
            'key' => (string) Str::uuid(),
            'id' => null,
            'sku' => '',
            'price' => 0,
            'quantity' => 1000,
            'selected_value_indexes' => $indexes,
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
        if ($this->variants === []) {
            $this->addVariant();
        }
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

        foreach ($this->optionGroups as $gi => $group) {
            foreach ($group['values'] as $vi => $val) {
                if (($val['product_image_id'] ?? null) === $id) {
                    $this->optionGroups[$gi]['values'][$vi]['product_image_id'] = null;
                }
            }
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
        $groupCount = count($this->optionGroups);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_active' => ['required', 'boolean'],
            'optionGroups' => ['required', 'array', 'min:1'],
            'optionGroups.*.name' => ['required', 'string', 'max:255'],
            'optionGroups.*.display_type' => ['required', 'in:text,swatch_color,swatch_image'],
            'optionGroups.*.values' => ['required', 'array', 'min:1'],
            'optionGroups.*.values.*.label' => ['required', 'string', 'max:255'],
            'optionGroups.*.values.*.hex_color' => ['nullable', 'string', 'max:7'],
            'optionGroups.*.values.*.product_image_id' => ['nullable'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.quantity' => ['required', 'integer', 'min:0'],
            'variants.*.selected_value_indexes' => ['required', 'array', 'size:'.$groupCount],
            'newImages.*' => ['nullable', 'image', 'max:5125'],
        ];

        foreach ($this->variants as $vi => $variantRow) {
            $rules["variants.{$vi}.sku"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_variants', 'sku')->ignore($variantRow['id']),
            ];
            foreach (array_keys($this->optionGroups) as $gi) {
                $valueCount = count($this->optionGroups[$gi]['values']);
                $rules["variants.{$vi}.selected_value_indexes.{$gi}"] = ['required', 'integer', 'min:0', 'max:'.max(0, $valueCount - 1)];
            }
        }

        $this->validate($rules);

        foreach ($this->optionGroups as $gi => $group) {
            foreach ($group['values'] as $vi => $val) {
                $hex = trim((string) ($val['hex_color'] ?? ''));
                if ($hex !== '' && ! preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
                    $this->addError("optionGroups.{$gi}.values.{$vi}.hex_color", __('Use a #RRGGBB color (e.g. #ef4444).'));

                    return;
                }
            }
        }

        $signatures = [];
        foreach ($this->variants as $variantRow) {
            $parts = [];
            foreach ($variantRow['selected_value_indexes'] as $gi => $vidx) {
                $parts[] = $gi.':'.$vidx;
            }
            sort($parts);
            $sig = implode('|', $parts);
            if (isset($signatures[$sig])) {
                Flux::toast(variant: 'danger', text: __('Each variant must be a unique combination of options.'));

                return;
            }
            $signatures[$sig] = true;
        }

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

            foreach ($this->optionGroups as $gi => &$groupRow) {
                foreach ($groupRow['values'] as $vi => &$valRow) {
                    $raw = $valRow['product_image_id'] ?? null;
                    if ($raw === null || $raw === '') {
                        continue;
                    }
                    $raw = (string) $raw;
                    if (str_starts_with($raw, 'new:')) {
                        $n = (int) Str::after($raw, 'new:');
                        $valRow['product_image_id'] = $newIds[$n] ?? null;
                    }
                }
                unset($valRow);
            }
            unset($groupRow);

            /** @var array<int, array<int, int>> $valueIdsByGroupAndIndex */
            $valueIdsByGroupAndIndex = [];

            $keptGroupIds = [];

            foreach ($this->optionGroups as $gi => $groupRow) {
                if (! empty($groupRow['id'])) {
                    $group = ProductOptionGroup::query()
                        ->whereKey($groupRow['id'])
                        ->where('product_id', $product->id)
                        ->firstOrFail();
                    $group->update([
                        'name' => $groupRow['name'],
                        'sort_order' => $gi,
                        'display_type' => $groupRow['display_type'],
                    ]);
                } else {
                    $group = ProductOptionGroup::query()->create([
                        'product_id' => $product->id,
                        'name' => $groupRow['name'],
                        'sort_order' => $gi,
                        'display_type' => $groupRow['display_type'],
                    ]);
                    $this->optionGroups[$gi]['id'] = $group->id;
                }

                $keptGroupIds[] = $group->id;

                $valueIdsByGroupAndIndex[$gi] = [];
                $keptValueIds = [];

                foreach ($groupRow['values'] as $vi => $valRow) {
                    $hex = trim((string) ($valRow['hex_color'] ?? ''));
                    $hex = $hex === '' ? null : $hex;
                    $rawImg = $valRow['product_image_id'] ?? null;
                    $imgId = null;
                    if ($rawImg !== null && $rawImg !== '') {
                        $imgId = is_numeric($rawImg) ? (int) $rawImg : null;
                        if ($imgId !== null && ! ProductImage::query()->where('product_id', $product->id)->whereKey($imgId)->exists()) {
                            $imgId = null;
                        }
                    }
                    if ($groupRow['display_type'] !== ProductOptionGroup::DISPLAY_SWATCH_COLOR) {
                        $hex = null;
                    }
                    if ($groupRow['display_type'] !== ProductOptionGroup::DISPLAY_SWATCH_IMAGE) {
                        $imgId = null;
                    }

                    if (! empty($valRow['id'])) {
                        $value = ProductOptionValue::query()
                            ->whereKey($valRow['id'])
                            ->where('product_option_group_id', $group->id)
                            ->firstOrFail();
                        $value->update([
                            'label' => $valRow['label'],
                            'sort_order' => $vi,
                            'hex_color' => $hex,
                            'product_image_id' => $imgId,
                        ]);
                    } else {
                        $value = $group->values()->create([
                            'label' => $valRow['label'],
                            'sort_order' => $vi,
                            'hex_color' => $hex,
                            'product_image_id' => $imgId,
                        ]);
                        $this->optionGroups[$gi]['values'][$vi]['id'] = $value->id;
                    }

                    $keptValueIds[] = $value->id;
                    $valueIdsByGroupAndIndex[$gi][$vi] = $value->id;
                }

                $group->values()->whereNotIn('id', $keptValueIds)->delete();
            }

            ProductOptionGroup::query()
                ->where('product_id', $product->id)
                ->whereNotIn('id', $keptGroupIds)
                ->delete();

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

                if (! empty($variantData['id'])) {
                    $variant = ProductVariant::query()->findOrFail($variantData['id']);
                    $variant->update([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'price' => $variantData['price'],
                        'is_active' => true,
                    ]);
                } else {
                    $variant = ProductVariant::query()->create([
                        'product_id' => $product->id,
                        'name' => '—',
                        'sku' => $sku,
                        'price' => $variantData['price'],
                        'is_active' => true,
                    ]);
                }

                ProductVariantOptionSelection::query()->where('product_variant_id', $variant->id)->delete();

                foreach (array_keys($this->optionGroups) as $gi) {
                    $gId = (int) ($this->optionGroups[$gi]['id'] ?? 0);
                    $vIdx = (int) ($variantData['selected_value_indexes'][$gi] ?? 0);
                    $valueId = $valueIdsByGroupAndIndex[$gi][$vIdx] ?? null;
                    if ($valueId === null || $gId === 0) {
                        continue;
                    }
                    ProductVariantOptionSelection::query()->create([
                        'product_variant_id' => $variant->id,
                        'product_option_group_id' => $gId,
                        'product_option_value_id' => $valueId,
                    ]);
                }

                ProductOptionStructure::syncVariantNameFromSelections($variant->fresh());

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
        $imageChoices = collect($this->existingImages)
            ->map(fn (array $img) => ['id' => $img['id'], 'label' => __('Image #:id', ['id' => $img['id']])])
            ->all();

        foreach ($this->newImages as $idx => $_) {
            $imageChoices[] = ['id' => 'new:'.$idx, 'label' => __('New upload #:n', ['n' => $idx + 1])];
        }

        return view('livewire.admin.products.form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'swatchImageChoices' => $imageChoices,
        ]);
    }
}
