<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
    case DOCUMENT_REVIEW = 4;
    case NEEDS_UPDATE = 5;
    case INFECTED_DOCUMENTS = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
            self::NEEDS_UPDATE => 'Necesita Actualización',
            self::DOCUMENT_REVIEW => 'Revisión Documentos',
            self::INFECTED_DOCUMENTS => 'Documentos Infectados (Inactivo)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::NEEDS_UPDATE => 'orange',
            self::DOCUMENT_REVIEW => 'blue',
            self::INFECTED_DOCUMENTS => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
            self::DOCUMENT_REVIEW => 'heroicon-o-document-magnifying-glass',
            self::INFECTED_DOCUMENTS => 'heroicon-o-shield-exclamation',
        };
    }
}
