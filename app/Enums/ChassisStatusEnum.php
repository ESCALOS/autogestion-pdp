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

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactivo',
            self::ACTIVE => 'Activo',
            self::NEEDS_UPDATE => 'Necesita Actualización',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INACTIVE => 'danger',
            self::ACTIVE => 'success',
            self::NEEDS_UPDATE => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INACTIVE => 'heroicon-o-x-circle',
            self::ACTIVE => 'heroicon-o-check-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
        };
    }
}
