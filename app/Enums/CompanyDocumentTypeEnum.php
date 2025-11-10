<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CompanyDocumentTypeEnum: int implements HasColor, HasIcon, HasLabel
{
    case RUC_RECORD = 1;
    case REPRESENTATIVE_DNI = 2;
    case FICHA_SUNARP = 3;
    case POWER_OF_ATTORNEY_VALIDITY = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::RUC_RECORD => 'Ficha RUC',
            self::REPRESENTATIVE_DNI => 'DNI Representante',
            self::FICHA_SUNARP => 'Ficha SUNARP',
            self::POWER_OF_ATTORNEY_VALIDITY => 'Vigencia de Poder',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::RUC_RECORD => 'primary',
            self::REPRESENTATIVE_DNI => 'secondary',
            self::FICHA_SUNARP => 'tertiary',
            self::POWER_OF_ATTORNEY_VALIDITY => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::RUC_RECORD => 'heroicon-o-document-text',
            self::REPRESENTATIVE_DNI => 'heroicon-o-identification',
            self::FICHA_SUNARP => 'heroicon-o-building-library',
            self::POWER_OF_ATTORNEY_VALIDITY => 'heroicon-o-briefcase',
        };
    }
}
