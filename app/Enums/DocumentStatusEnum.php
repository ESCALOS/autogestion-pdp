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
    case NEEDS_UPDATE = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
            self::NEEDS_UPDATE => 'Necesita Actualización',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::NEEDS_UPDATE => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::NEEDS_UPDATE => 'heroicon-o-exclamation-circle',
        };
    }
}
