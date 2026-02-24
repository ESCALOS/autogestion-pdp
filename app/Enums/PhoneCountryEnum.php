<?php

declare(strict_types=1);

namespace App\Enums;

enum PhoneCountryEnum: string
{
    case PERU = '+51';
    case COLOMBIA = '+57';
    case CHILE = '+56';
    case ARGENTINA = '+54';
    case MEXICO = '+52';
    case ESPAÑA = '+34';
    case ECUADOR = '+593';
    case BOLIVIA = '+591';
    case PARAGUAY = '+595';
    case URUGUAY = '+598';
    case VENEZUELA = '+58';
    case PANAMA = '+507';
    case COSTA_RICA = '+506';
    case GUATEMALA = '+502';
    case EL_SALVADOR = '+503';
    case HONDURAS = '+504';
    case NICARAGUA = '+505';
    case DOMINICANA = '+1-809';
    case CUBA = '+53';
    case JAMAICA = '+1-876';
    case TRINIDAD_TOBAGO = '+1-868';
    case BARBADOS = '+1-246';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->getLabel().' '.$case->value,
            ])
            ->toArray();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PERU => 'Perú',
            self::COLOMBIA => 'Colombia',
            self::CHILE => 'Chile',
            self::ARGENTINA => 'Argentina',
            self::MEXICO => 'México',
            self::ESPAÑA => 'España',
            self::ECUADOR => 'Ecuador',
            self::BOLIVIA => 'Bolivia',
            self::PARAGUAY => 'Paraguay',
            self::URUGUAY => 'Uruguay',
            self::VENEZUELA => 'Venezuela',
            self::PANAMA => 'Panamá',
            self::COSTA_RICA => 'Costa Rica',
            self::GUATEMALA => 'Guatemala',
            self::EL_SALVADOR => 'El Salvador',
            self::HONDURAS => 'Honduras',
            self::NICARAGUA => 'Nicaragua',
            self::DOMINICANA => 'República Dominicana',
            self::CUBA => 'Cuba',
            self::JAMAICA => 'Jamaica',
            self::TRINIDAD_TOBAGO => 'Trinidad y Tobago',
            self::BARBADOS => 'Barbados',
        };
    }
}
