<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class DriverRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Driver $driver,
        public array $rejectedDocuments,
        public ?string $appealUrl = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documentos Rechazados - '.$this->driver->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
