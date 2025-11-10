<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ChassisStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case INACTIVE = 1;
    case ACTIVE = 2;
    case NEEDS_UPDATE = 3;
    case PENDING_APPROVAL = 4;
    case DOCUMENT_REVIEW = 5;
    case INFECTED_DOCUMENTS = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactivo',
            self::ACTIVE => 'Activo',
            self::NEEDS_UPDATE => 'Necesita Actualización',
            self::PENDING_APPROVAL => 'Espera de aprobación',
            self::DOCUMENT_REVIEW => 'Revisión Documentos',
            self::INFECTED_DOCUMENTS => 'Documentos Infectados (Inactivo)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INACTIVE => 'danger',
            self::ACTIVE => 'success',
            self::NEEDS_UPDATE => 'warning',
            self::PENDING_APPROVAL => 'gray',
            self::DOCUMENT_REVIEW => 'info',
            self::INFECTED_DOCUMENTS => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INACTIVE => 'heroicon-o-x-circle',
            self::ACTIVE => 'heroicon-o-check-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
            self::PENDING_APPROVAL => 'heroicon-o-clock',
            self::DOCUMENT_REVIEW => 'heroicon-o-document-magnifying-glass',
            self::INFECTED_DOCUMENTS => 'heroicon-o-shield-exclamation',
        };
    }
}
