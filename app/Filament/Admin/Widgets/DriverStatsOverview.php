<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\Drivers\DriverResource;
use App\Models\Driver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DriverStatsOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(' ', Driver::where('status', EntityStatusEnum::INACTIVE)->count())
                ->description('Inactivos')
                ->descriptionIcon(EntityStatusEnum::INACTIVE->getIcon())
                ->color(EntityStatusEnum::INACTIVE->getColor())
                ->url(DriverResource::getUrl('index'). '?tab=rechazados'),

            Stat::make(' ', Driver::where('status', EntityStatusEnum::ACTIVE)->count())
                ->description('Activos')
                ->descriptionIcon(EntityStatusEnum::ACTIVE->getIcon())
                ->color(EntityStatusEnum::ACTIVE->getColor())
                ->url(DriverResource::getUrl('index') . '?tab=aprobados'),

            Stat::make(' ', Driver::where('status', EntityStatusEnum::NEEDS_UPDATE)->count())
                ->description('Necesitan Act.')
                ->descriptionIcon(EntityStatusEnum::NEEDS_UPDATE->getIcon())
                ->color(EntityStatusEnum::NEEDS_UPDATE->getColor())
                ->url(DriverResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Driver::where('status', EntityStatusEnum::PENDING_APPROVAL)->count())
                ->description('Pendientes de Aprob.')
                ->descriptionIcon(EntityStatusEnum::PENDING_APPROVAL->getIcon())
                ->color(EntityStatusEnum::PENDING_APPROVAL->getColor())
                ->url(DriverResource::getUrl('index') . '?tab=pendientes'),

            Stat::make(' ', Driver::where('status', EntityStatusEnum::REJECTED)->count())
                ->description('Rechazados')
                ->descriptionIcon(EntityStatusEnum::REJECTED->getIcon())
                ->color(EntityStatusEnum::REJECTED->getColor())
                ->url(DriverResource::getUrl('index'). '?tab=rechazados'),
        ];
    }
}
