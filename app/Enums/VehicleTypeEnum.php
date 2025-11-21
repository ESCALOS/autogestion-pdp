<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VehicleTypeEnum: string implements HasColor, HasLabel
{
    case C3 = 'C3';
    case CBAJA = 'CBAJA';
    case N1 = 'N1';
    case N3 = 'N3';
    case R4 = 'R4';
    case S2 = 'S2';
    case S3 = 'S3';
    case SE2 = 'SE2';
    case SE3 = 'SE3';
    case TRK = 'TRK';

    public function getLabel(): string
    {
        return match ($this) {
            self::C3 => 'C3',
            self::CBAJA => 'CBAJA',
            self::N1 => 'N1',
            self::N3 => 'N3',
            self::R4 => 'R4',
            self::S2 => 'S2',
            self::S3 => 'S3',
            self::SE2 => 'SE2',
            self::SE3 => 'SE3',
            self::TRK => 'TRK',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::C3 => 'primary',
            self::CBAJA => 'secondary',
            self::N1 => 'success',
            self::N3 => 'info',
            self::R4 => 'warning',
            self::S2 => 'danger',
            self::S3 => 'primary',
            self::SE2 => 'secondary',
            self::SE3 => 'success',
            self::TRK => 'info',
        };
    }
}
