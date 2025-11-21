<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\Trucks\TruckResource;
use App\Models\Truck;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TruckStatsOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(' ', Truck::where('status', EntityStatusEnum::INACTIVE)->count())
                ->description('Inactivos')
                ->descriptionIcon(EntityStatusEnum::INACTIVE->getIcon())
                ->color(EntityStatusEnum::INACTIVE->getColor())
                ->url(TruckResource::getUrl('index') . '?tab=rechazados'),

            Stat::make(' ', Truck::where('status', EntityStatusEnum::ACTIVE)->count())
                ->description('Activos')
                ->descriptionIcon(EntityStatusEnum::ACTIVE->getIcon())
                ->color(EntityStatusEnum::ACTIVE->getColor())
                ->url(TruckResource::getUrl('index') . '?tab=aprobados'),

            Stat::make(' ', Truck::where('status', EntityStatusEnum::NEEDS_UPDATE)->count())
                ->description('Necesitan Act.')
                ->descriptionIcon(EntityStatusEnum::NEEDS_UPDATE->getIcon())
                ->color(EntityStatusEnum::NEEDS_UPDATE->getColor())
                ->url(TruckResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Truck::where('status', EntityStatusEnum::PENDING_APPROVAL)->count())
                ->description('Pendientes de Aprob.')
                ->descriptionIcon(EntityStatusEnum::PENDING_APPROVAL->getIcon())
                ->color(EntityStatusEnum::PENDING_APPROVAL->getColor())
                ->url(TruckResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Truck::where('status', EntityStatusEnum::REJECTED)->count())
                ->description('Rechazados')
                ->descriptionIcon(EntityStatusEnum::REJECTED->getIcon())
                ->color(EntityStatusEnum::REJECTED->getColor())
                ->url(TruckResource::getUrl('index') . '?tab=rechazados'),
        ];
    }
}
