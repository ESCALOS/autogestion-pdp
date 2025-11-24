<?php

namespace App\Mail;

use App\Models\Chassis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChassisApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Chassis $chassis
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Carreta Aprobada - ' . $this->chassis->license_plate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chassis-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
