<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUserConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public array $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We Received Your Inquiry - Sirateq Ghana Group Ltd.',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.user',
            with: [
                'formData' => $this->formData,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
