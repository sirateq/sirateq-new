<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ProductOptionStructure
{
    /**
     * Ensure a variant is linked to a single option value under a shared "Option" group (import / factories).
     */
    public static function attachVariantToOptionGroup(
        Product $product,
        ProductVariant $variant,
        string $groupName,
        string $valueLabel,
        string $displayType = ProductOptionGroup::DISPLAY_TEXT,
    ): void {
        DB::transaction(function () use ($product, $variant, $groupName, $valueLabel, $displayType): void {
            $group = ProductOptionGroup::query()->firstOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $groupName,
                ],
                [
                    'sort_order' => (int) (ProductOptionGroup::query()->where('product_id', $product->id)->max('sort_order') ?? -1) + 1,
                    'display_type' => $displayType,
                ],
            );

            $sort = (int) ($group->values()->max('sort_order') ?? -1) + 1;

            $value = $group->values()->create([
                'label' => $valueLabel,
                'sort_order' => $sort,
            ]);

            ProductVariantOptionSelection::query()->updateOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'product_option_group_id' => $group->id,
                ],
                [
                    'product_option_value_id' => $value->id,
                ],
            );

            static::syncVariantNameFromSelections($variant);
        });
    }

    public static function syncVariantNameFromSelections(ProductVariant $variant): void
    {
        $variant->load([
            'optionSelections.group',
            'optionSelections.value',
        ]);

        $labels = $variant->optionSelections
            ->sortBy(fn (ProductVariantOptionSelection $s) => $s->group->sort_order * 100000 + $s->group->id)
            ->map(fn (ProductVariantOptionSelection $s) => $s->value->label)
            ->values();

        $name = $labels->implode(' / ');
        if ($name !== '' && $name !== $variant->name) {
            $variant->update(['name' => $name]);
        }
    }
}
