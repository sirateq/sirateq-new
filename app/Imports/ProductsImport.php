<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductsImport implements OnEachRow, WithHeadingRow
{
    public int $importedCount = 0;

    public int $skippedCount = 0;

    /**
     * @var list<string>
     */
    public array $skipMessages = [];

    public function onRow(Row $row): void
    {
        $cells = $row->toArray();
        $name = isset($cells['name']) ? trim((string) $cells['name']) : '';
        $categoryLabel = isset($cells['category']) ? trim((string) $cells['category']) : '';

        if ($name === '' && $categoryLabel === '') {
            return;
        }

        if ($name === '' || $categoryLabel === '') {
            $this->skipWithMessage($row->getIndex(), __('Each row needs both name and category.'));

            return;
        }

        $category = Category::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($categoryLabel)])
            ->first();

        if ($category === null) {
            $this->skipWithMessage($row->getIndex(), __('Unknown category ":name".', ['name' => $categoryLabel]));

            return;
        }

        DB::transaction(function () use ($name, $category): void {
            $product = Product::query()->create([
                'category_id' => $category->id,
                'name' => $name,
                'description' => null,
                'is_active' => true,
            ]);

            $variant = ProductVariant::query()->create([
                'product_id' => $product->id,
                'name' => 'Default',
                'sku' => $this->uniqueImportSku($product->id),
                'price' => 0,
                'is_active' => true,
            ]);

            InventoryItem::query()->create([
                'product_variant_id' => $variant->id,
                'quantity' => 0,
            ]);

            $this->importedCount++;
        });
    }

    private function uniqueImportSku(int $productId): string
    {
        $base = 'IMP-'.$productId;
        if (! ProductVariant::query()->where('sku', $base)->exists()) {
            return $base;
        }

        return $base.'-'.Str::upper(Str::random(4));
    }

    private function skipWithMessage(int $rowIndex, string $message): void
    {
        $this->skippedCount++;
        if (count($this->skipMessages) < 15) {
            $this->skipMessages[] = __('Row :row: :message', ['row' => $rowIndex, 'message' => $message]);
        }
    }
}
