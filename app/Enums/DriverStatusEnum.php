<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DriverStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case INACTIVE = 1;
    case ACTIVE = 2;
    case PENDING_APPROVAL = 3;
    case DOCUMENT_REVIEW = 4;
    case NEEDS_UPDATE = 5;
    case INFECTED_DOCUMENTS = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactivo',
            self::ACTIVE => 'Activo',
            self::PENDING_APPROVAL => 'Pendiente de Aprobación',
            self::DOCUMENT_REVIEW => 'Revisión Documentos',
            self::NEEDS_UPDATE => 'Necesita Actualización',
            self::INFECTED_DOCUMENTS => 'Documentos Infectados (Inactivo)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING_APPROVAL => 'warning',
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::NEEDS_UPDATE => 'orange',
            self::DOCUMENT_REVIEW => 'blue',
            self::INFECTED_DOCUMENTS => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING_APPROVAL => 'heroicon-o-clock',
            self::ACTIVE => 'heroicon-o-check-circle',
            self::INACTIVE => 'heroicon-o-x-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
            self::DOCUMENT_REVIEW => 'heroicon-o-document-magnifying-glass',
            self::INFECTED_DOCUMENTS => 'heroicon-o-shield-exclamation',
        };
    }
}
