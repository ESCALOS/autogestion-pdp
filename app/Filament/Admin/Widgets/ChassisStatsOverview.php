<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\Chassis\ChassisResource;
use App\Models\Chassis;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChassisStatsOverview extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(' ', Chassis::where('status', EntityStatusEnum::INACTIVE)->count())
                ->description('Inactivas')
                ->descriptionIcon(EntityStatusEnum::INACTIVE->getIcon())
                ->color(EntityStatusEnum::INACTIVE->getColor())
                ->url(ChassisResource::getUrl('index') . '?tab=rechazados'),

            Stat::make(' ', Chassis::where('status', EntityStatusEnum::ACTIVE)->count())
                ->description('Activas')
                ->descriptionIcon(EntityStatusEnum::ACTIVE->getIcon())
                ->color(EntityStatusEnum::ACTIVE->getColor())
                ->url(ChassisResource::getUrl('index') . '?tab=aprobados'),

            Stat::make(' ', Chassis::where('status', EntityStatusEnum::NEEDS_UPDATE)->count())
                ->description('Necesitan Act.')
                ->descriptionIcon(EntityStatusEnum::NEEDS_UPDATE->getIcon())
                ->color(EntityStatusEnum::NEEDS_UPDATE->getColor())
                ->url(ChassisResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Chassis::where('status', EntityStatusEnum::PENDING_APPROVAL)->count())
                ->description('Pendientes de Aprob.')
                ->descriptionIcon(EntityStatusEnum::PENDING_APPROVAL->getIcon())
                ->color(EntityStatusEnum::PENDING_APPROVAL->getColor())
                ->url(ChassisResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Chassis::where('status', EntityStatusEnum::REJECTED)->count())
                ->description('Rechazadas')
                ->descriptionIcon(EntityStatusEnum::REJECTED->getIcon())
                ->color(EntityStatusEnum::REJECTED->getColor())
                ->url(ChassisResource::getUrl('index') . '?tab=rechazados'),
        ];
    }
}
