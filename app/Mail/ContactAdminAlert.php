<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAdminAlert extends Mailable
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
            subject: 'New Project Inquiry: '.$this->formData['first_name'].' '.$this->formData['last_name'],
            replyTo: [
                new Address($this->formData['email'], $this->formData['first_name'].' '.$this->formData['last_name']),
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact.admin',
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
