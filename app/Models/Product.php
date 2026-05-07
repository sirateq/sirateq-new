<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'name', 'slug', 'description', 'is_active'])]
class Product extends Model
{
    use HasFactory, Sluggable;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    public function optionGroups(): HasMany
    {
        return $this->hasMany(ProductOptionGroup::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getMainImageUrlAttribute(): ?string
    {
        $primary = $this->relationLoaded('images')
            ? ($this->images->firstWhere('is_primary', true) ?? $this->images->first())
            : ($this->primaryImage()->first() ?? $this->images()->first());

        return $primary?->url;
    }

    /**
     * Storefront-safe HTML for the product description: GitHub-Flavored Markdown by default.
     * If the trimmed body starts with an HTML tag (trusted admin content), it is output as-is.
     */
    public function renderedDescriptionHtml(): HtmlString
    {
        $raw = trim((string) ($this->description ?? ''));
        if ($raw === '') {
            return new HtmlString('');
        }

        if ($this->descriptionAppearsToStartWithHtml($raw)) {
            return new HtmlString($raw);
        }

        return new HtmlString(Str::markdown($raw));
    }

    /**
     * Plain text for tables and list excerpts (tags stripped from rendered output).
     */
    public function descriptionPlainExcerpt(int $limit = 160): string
    {
        $raw = trim((string) ($this->description ?? ''));
        if ($raw === '') {
            return '';
        }

        $plain = strip_tags((string) $this->renderedDescriptionHtml());
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? $plain;

        return Str::limit(trim((string) $plain), $limit);
    }

    /**
     * Whether the product has no sellable quantity on any variant (used on catalog cards).
     *
     * @param  Collection<int, ProductVariant>|null  $variants
     */
    public function isOutOfStock(?Collection $variants = null): bool
    {
        $variants ??= $this->variants;

        if ($variants->isEmpty()) {
            return true;
        }

        foreach ($variants as $variant) {
            $qty = (int) optional($variant->inventoryItem)->quantity;
            if ($qty > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Price line for listing cards: GH₵99.00 or GH₵40.00 – 200.00 when variant prices differ.
     */
    public function storefrontVariantPriceLabel(): string
    {
        $variants = $this->variants;

        if ($variants->isEmpty()) {
            return 'GH₵'.number_format(0.0, 2);
        }

        $min = (float) $variants->min('price');
        $max = (float) $variants->max('price');
        $minFormatted = number_format($min, 2);

        if ($variants->count() === 1 || abs($max - $min) < 0.005) {
            return 'GH₵'.$minFormatted;
        }

        return 'GH₵'.$minFormatted.' – '.number_format($max, 2);
    }

    private function descriptionAppearsToStartWithHtml(string $text): bool
    {
        $trim = ltrim($text);

        return $trim !== '' && (bool) preg_match('/^<[a-zA-Z]/', $trim);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
