<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $search = '',
        private readonly string $status = '',
        private readonly string $sortBy = 'created_at',
        private readonly string $sortDirection = 'desc',
    ) {}

    public function query(): Builder
    {
        $sortBy = $this->normalizedSortBy();
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return Product::query()
            ->with(['category', 'variants'])
            ->when($this->search !== '', function (Builder $query): void {
                $term = $this->search;
                $query->where(function (Builder $q) use ($term): void {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhereHas('variants', fn (Builder $v) => $v->where('sku', 'like', "%{$term}%"));
                });
            })
            ->when($this->status === 'active', fn (Builder $q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn (Builder $q) => $q->where('is_active', false))
            ->orderBy($sortBy, $sortDirection);
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Category',
            'Description',
            'Active (1=yes)',
            'Variant count',
            'SKUs',
            'Min price',
            'Max price',
        ];
    }

    /**
     * @param  Product  $product
     * @return list<string|float|int|null>
     */
    public function map($product): array
    {
        $skus = $product->variants->pluck('sku')->filter()->implode(', ');
        $prices = $product->variants->pluck('price');

        return [
            $product->id,
            $product->name,
            $product->slug,
            $product->category?->name ?? '',
            (string) ($product->description ?? ''),
            $product->is_active ? 1 : 0,
            $product->variants->count(),
            $skus,
            $prices->isNotEmpty() ? (float) $prices->min() : null,
            $prices->isNotEmpty() ? (float) $prices->max() : null,
        ];
    }

    private function normalizedSortBy(): string
    {
        $allowed = ['name', 'created_at', 'is_active'];

        return in_array($this->sortBy, $allowed, true) ? $this->sortBy : 'created_at';
    }
}
