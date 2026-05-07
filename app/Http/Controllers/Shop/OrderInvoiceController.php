<?php

namespace App\Http\Controllers\Shop;

use App\Models\Order;
use App\Services\OrderInvoicePdfExporter;
use Symfony\Component\HttpFoundation\Response;

class OrderInvoiceController
{
    public function __invoke(Order $order): Response
    {
        if (! $order->isAccessibleByCurrentCustomer()) {
            abort(404);
        }

        return OrderInvoicePdfExporter::downloadResponse($order);
    }
}
