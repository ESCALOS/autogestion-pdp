<?php

declare(strict_types=1);

namespace App\Enums;

enum MachineryOperationTypeEnum: int
{
    case IRON_STONE = 1;
    case CORN = 2;
    case CLINKER = 3;
    case COAL = 4;
    case COPPER = 5;
    case ZINC = 6;
    case LEAD = 7;
    case ALUMINUM = 8;
    case GRAINS = 9;
    case FERTILIZERS = 10;

    public function getLabel(): ?string
    {
        return match ($this) {
            MachineryOperationTypeEnum::IRON_STONE => 'Piedra de Hierro',
            MachineryOperationTypeEnum::CORN => 'Maíz',
            MachineryOperationTypeEnum::CLINKER => 'Clinker',
            MachineryOperationTypeEnum::COAL => 'Carbón',
            MachineryOperationTypeEnum::COPPER => 'Cobre',
            MachineryOperationTypeEnum::ZINC => 'Zinc',
            MachineryOperationTypeEnum::LEAD => 'Plomo',
            MachineryOperationTypeEnum::ALUMINUM => 'Aluminio',
            MachineryOperationTypeEnum::GRAINS => 'Granos',
            MachineryOperationTypeEnum::FERTILIZERS => 'Fertilizantes',
        };
    }
}
