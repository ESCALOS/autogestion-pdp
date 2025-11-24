<?php

namespace App\Mail;

use App\Models\Truck;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TruckRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Truck $truck,
        public array $rejectedDocuments,
        public ?string $appealUrl = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documentos de Tracto Rechazados - ' . $this->truck->license_plate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.truck-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
