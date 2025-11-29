<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ChassisDocumentsExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public array $chassis,
        public int $daysToExpiration
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Documentos de Chassis Próximos a Vencer - {$this->daysToExpiration} días",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chassis-documents-expiring',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
