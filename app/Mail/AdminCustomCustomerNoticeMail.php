<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCustomCustomerNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $noticeSubject,
        public string $markdownBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->noticeSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.admin-custom-notice',
            with: [
                'order' => $this->order,
                'markdownBody' => $this->markdownBody,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
