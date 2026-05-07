<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfDocument;
use Symfony\Component\HttpFoundation\Response;

class OrderInvoicePdfExporter
{
    public static function load(Order $order): DomPdfDocument
    {
        $order->loadMissing(['items', 'payments', 'coupon']);
        $pdf = Pdf::loadView('pdf.order-invoice', [
            'order' => $order,
        ]);
        $pdf->setPaper('a4');

        return $pdf;
    }

    public static function binary(Order $order): string
    {
        return self::load($order)->output();
    }

    public static function downloadResponse(Order $order): Response
    {
        $filename = 'invoice-'.$order->order_number.'.pdf';

        return self::load($order)->download($filename);
    }
}
