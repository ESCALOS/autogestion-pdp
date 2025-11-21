<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TruckTypeEnum: string implements HasColor, HasLabel
{
    case EIGHT_X_FOUR = '8x4';
    case B2 = 'B2';
    case B3_1 = 'B3-1';
    case B4_1 = 'B4-1';
    case BA_1 = 'BA-1';
    case C2 = 'C2';
    case C3 = 'C3';
    case C4 = 'C4';
    case CAMABAJA = 'Camabaja';
    case N1 = 'N1';
    case N2 = 'N2';
    case N3 = 'N3';
    case T_ESPECIAL = 'T-Especial';
    case T2 = 'T2';
    case T3 = 'T3';
    case T4 = 'T4';
    case T4_ESPECIAL = 'T4 Especial';
    case TE2 = 'TE2';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::EIGHT_X_FOUR => 'primary',
            self::B2 => 'secondary',
            self::B3_1 => 'success',
            self::B4_1 => 'info',
            self::BA_1 => 'warning',
            self::C2 => 'danger',
            self::C3 => 'primary',
            self::C4 => 'secondary',
            self::CAMABAJA => 'success',
            self::N1 => 'info',
            self::N2 => 'warning',
            self::N3 => 'danger',
            self::T_ESPECIAL => 'primary',
            self::T2 => 'secondary',
            self::T3 => 'success',
            self::T4 => 'info',
            self::T4_ESPECIAL => 'warning',
            self::TE2 => 'danger',
        };
    }
}
