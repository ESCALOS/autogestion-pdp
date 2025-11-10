<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RequestStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 1;
    case SUBMITTED = 2;
    case IN_REVIEW = 3;
    case APPROVED = 4;
    case REJECTED = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::SUBMITTED => 'Enviado',
            self::IN_REVIEW => 'En Revisión',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'info',
            self::IN_REVIEW => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document',
            self::SUBMITTED => 'heroicon-o-paper-airplane',
            self::IN_REVIEW => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
        };
    }
}
