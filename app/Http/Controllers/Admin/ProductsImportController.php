<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ImportProductsRequest;
use App\Imports\ProductsImport;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class ProductsImportController
{
    public function __invoke(ImportProductsRequest $request): RedirectResponse
    {
        $import = new ProductsImport;
        Excel::import($import, $request->file('file'));

        $message = __('Imported :count products.', ['count' => $import->importedCount]);
        if ($import->skippedCount > 0) {
            $message .= ' '.__('Skipped :count rows.', ['count' => $import->skippedCount]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('import_status', $message)
            ->with('import_errors', $import->skipMessages);
    }
}
