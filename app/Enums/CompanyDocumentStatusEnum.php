<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CompanyDocumentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case PENDIENTE = 1;
    case APROBADO = 2;
    case RECHAZADO = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::APROBADO => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDIENTE => 'warning',
            self::APROBADO => 'success',
            self::RECHAZADO => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDIENTE => 'heroicon-o-clock',
            self::APROBADO => 'heroicon-o-check-circle',
            self::RECHAZADO => 'heroicon-o-x-circle',
        };
    }
}
