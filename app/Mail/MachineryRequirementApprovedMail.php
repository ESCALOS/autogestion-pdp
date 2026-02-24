<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MachineryRequirement;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class MachineryRequirementApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MachineryRequirement $requirement,
        public Supplier $supplier
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nuevo Requerimiento de Maquinaria - {$this->requirement->vessel_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.machinery-requirement-approved',
            with: [
                'requirement' => $this->requirement,
                'supplier' => $this->supplier,
                'unitType' => $this->requirement->unit_type->getLabel(),
                'activationTime' => $this->requirement->activation_time->format('d/m/Y H:i'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
