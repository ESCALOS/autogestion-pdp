<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum EntityStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case INACTIVE = 1;
    case ACTIVE = 2;
    case NEEDS_UPDATE = 3;
    case PENDING_APPROVAL = 4;
    case DOCUMENT_REVIEW = 5;
    case REJECTED = 6;
    case INFECTED_DOCUMENTS = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactivo',
            self::ACTIVE => 'Activo',
            self::NEEDS_UPDATE => 'Necesita Actualización',
            self::PENDING_APPROVAL => 'Pendiente de Aprobación',
            self::DOCUMENT_REVIEW => 'Revisión de Documentos',
            self::REJECTED => 'Documentos Rechazados',
            self::INFECTED_DOCUMENTS => 'Documentos Infectados',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INACTIVE => 'danger',
            self::ACTIVE => 'success',
            self::NEEDS_UPDATE => 'warning',
            self::PENDING_APPROVAL => 'info',
            self::DOCUMENT_REVIEW => 'primary',
            self::REJECTED => 'danger',
            self::INFECTED_DOCUMENTS => 'dark',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INACTIVE => 'heroicon-o-x-circle',
            self::ACTIVE => 'heroicon-o-check-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
            self::PENDING_APPROVAL => 'heroicon-o-clock',
            self::DOCUMENT_REVIEW => 'heroicon-o-document',
            self::REJECTED => 'heroicon-o-x-circle',
            self::INFECTED_DOCUMENTS => 'heroicon-o-shield-exclamation',
        };
    }
}
