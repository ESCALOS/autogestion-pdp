<?php

namespace App\Mail;

use App\Models\Chassis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChassisRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Chassis $chassis,
        public array $rejectedDocuments,
        public ?string $appealUrl = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documentos Rechazados - ' . $this->chassis->license_plate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chassis-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
