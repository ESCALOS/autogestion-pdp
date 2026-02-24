<?php

declare(strict_types=1);

namespace App\Enums;

enum MachineryUnitTypeEnum: int
{
    case UNITS_WITH_HOPPER = 1;
    case PLATFORM = 2;
    case HEAVY_MACHINERY = 3;
    case CRANE = 4;
    case FORKLIFT = 5;
    case BULLDOZER = 6;
    case EXCAVATOR = 7;

    public function getLabel(): ?string
    {
        return match ($this) {
            MachineryUnitTypeEnum::UNITS_WITH_HOPPER => 'Unidades con Tolva',
            MachineryUnitTypeEnum::PLATFORM => 'Plataforma',
            MachineryUnitTypeEnum::HEAVY_MACHINERY => 'Maquinaria Pesada',
            MachineryUnitTypeEnum::CRANE => 'Grúa',
            MachineryUnitTypeEnum::FORKLIFT => 'Montacargas',
            MachineryUnitTypeEnum::BULLDOZER => 'Bulldozer',
            MachineryUnitTypeEnum::EXCAVATOR => 'Excavadora',
        };
    }
}
