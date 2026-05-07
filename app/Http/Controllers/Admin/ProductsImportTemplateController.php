<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductsImportTemplateController
{
    public function __invoke(): BinaryFileResponse
    {
        return Excel::download(
            new ProductsImportTemplateExport,
            'products-import-template.xlsx',
        );
    }
}
