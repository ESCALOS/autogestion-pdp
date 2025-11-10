<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CompanyTypeEnum: int implements HasColor, HasIcon, HasLabel
{
    case NATURAL = 1;
    case JURIDICA = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::NATURAL => 'Empresa Natural',
            self::JURIDICA => 'Empresa Jurídica',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NATURAL => 'primary',
            self::JURIDICA => 'secondary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NATURAL => 'heroicon-o-user',
            self::JURIDICA => 'heroicon-o-building-office',
        };
    }
}
