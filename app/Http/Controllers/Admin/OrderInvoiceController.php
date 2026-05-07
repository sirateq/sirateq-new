<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Services\OrderInvoicePdfExporter;
use Symfony\Component\HttpFoundation\Response;

class OrderInvoiceController
{
    public function __invoke(Order $order): Response
    {
        return OrderInvoicePdfExporter::downloadResponse($order);
    }
}
