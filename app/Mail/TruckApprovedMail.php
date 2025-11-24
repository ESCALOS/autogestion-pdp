<?php

namespace App\Mail;

use App\Models\Truck;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TruckApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Truck $truck
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tracto Aprobado - ' . $this->truck->license_plate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.truck-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
