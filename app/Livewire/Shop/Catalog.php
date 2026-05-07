<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Shop')]
class Catalog extends Component
{
    #[Url(as: 'sort')]
    public string $sort = 'default';

    #[Url(as: 'cat')]
    public ?int $categoryId = null;

    #[Url(as: 'view')]
    public string $view = 'grid';

    #[Url(as: 'min')]
    public ?float $minPrice = null;

    #[Url(as: 'max')]
    public ?float $maxPrice = null;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $filterOpen = false;

    public function setView(string $view): void
    {
        $this->view = in_array($view, ['grid', 'list']) ? $view : 'grid';
    }

    public function toggleFilter(): void
    {
        $this->filterOpen = ! $this->filterOpen;
    }

    public function setCategory(?int $id): void
    {
        $this->categoryId = $id;
    }

    public function clearFilters(): void
    {
        $this->categoryId = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sort = 'default';
        $this->search = '';
    }

    #[Computed]
    public function products(): Collection
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['category', 'variants', 'images'])
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->latest()
            ->get()
            ->filter(function (Product $product): bool {
                $price = (float) $product->variants->min('price');

                if ($this->minPrice !== null && $price < $this->minPrice) {
                    return false;
                }
                if ($this->maxPrice !== null && $price > $this->maxPrice) {
                    return false;
                }

                $term = mb_strtolower(trim($this->search));
                if ($term !== '') {
                    $haystacks = [
                        mb_strtolower($product->name),
                        mb_strtolower(strip_tags((string) $product->description)),
                    ];
                    foreach ($product->variants as $variant) {
                        $haystacks[] = mb_strtolower($variant->sku);
                        $haystacks[] = mb_strtolower($variant->name);
                    }
                    $matched = false;
                    foreach ($haystacks as $h) {
                        if ($h !== '' && str_contains($h, $term)) {
                            $matched = true;
                            break;
                        }
                    }
                    if (! $matched) {
                        return false;
                    }
                }

                return true;
            });

        return match ($this->sort) {
            'price_asc' => $products->sortBy(fn (Product $p): float => (float) ($p->variants->min('price') ?? 0))->values(),
            'price_desc' => $products->sortByDesc(fn (Product $p): float => (float) ($p->variants->min('price') ?? 0))->values(),
            'newest' => $products->sortByDesc('created_at')->values(),
            'name' => $products->sortBy('name')->values(),
            default => $products->values(),
        };
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::query()->where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.shop.catalog');
    }
}
