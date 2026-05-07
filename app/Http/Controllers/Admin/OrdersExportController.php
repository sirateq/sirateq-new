<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrdersExportController
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $sortBy = (string) $request->query('sort_by', 'created_at');
        $sortDirection = strtolower((string) $request->query('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        return Excel::download(
            new OrdersExport(
                search: (string) $request->query('q', ''),
                status: (string) $request->query('status', ''),
                sortBy: $sortBy,
                sortDirection: $sortDirection,
            ),
            'orders-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }
}
