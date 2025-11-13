<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public array $rejectedDocuments,
        public string $appealUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Empresa Rechazada - ' . $this->company->business_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
