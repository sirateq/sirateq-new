<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable(['product_id', 'name', 'sku', 'price', 'is_active'])]
class ProductVariant extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryItem(): HasOne
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function optionSelections(): HasMany
    {
        return $this->hasMany(ProductVariantOptionSelection::class, 'product_variant_id');
    }

    /**
     * Generate a globally unique SKU for variants when none is provided (prefix SQ-).
     */
    public static function generateUniqueSku(): string
    {
        for ($attempt = 0; $attempt < 15; $attempt++) {
            $sku = 'SQ-'.strtoupper(Str::random(10));
            if (! self::query()->where('sku', $sku)->exists()) {
                return $sku;
            }
        }

        return 'SQ-'.str_replace('-', '', (string) Str::uuid());
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->product->name} - {$this->name}");
    }
}
