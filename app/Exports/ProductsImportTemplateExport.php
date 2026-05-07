<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsImportTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return list<list<string>>
     */
    public function array(): array
    {
        return [
            [__('LED desk lamp'), __('Home & Office')],
        ];
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['name', 'category'];
    }
}
