<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CompanyApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Empresa Aprobada - '.$this->company->business_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
