<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\OrderInvoicePdfExporter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Order #:number confirmed — :app', [
                'number' => $this->order->order_number,
                'app' => config('app.name'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.placed-customer',
            with: [
                'order' => $this->order,
                'orderUrl' => $this->order->temporarySignedStorefrontUrl(),
                'invoiceUrl' => $this->order->temporarySignedInvoiceDownloadUrl(),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $filename = 'invoice-'.$this->order->order_number.'.pdf';

        return [
            Attachment::fromData(fn (): string => OrderInvoicePdfExporter::binary($this->order), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
