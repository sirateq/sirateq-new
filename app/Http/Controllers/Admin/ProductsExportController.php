<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductsExportController
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $sortBy = (string) $request->query('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->query('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        return Excel::download(
            new ProductsExport(
                search: (string) $request->query('q', ''),
                status: (string) $request->query('status', ''),
                sortBy: $sortBy,
                sortDirection: $sortDirection,
            ),
            'products-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }
}
