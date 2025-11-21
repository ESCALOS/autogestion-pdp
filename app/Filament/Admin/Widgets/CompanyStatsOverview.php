<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(' ', Company::where('status', 1)->count())
                ->description('Pendientes')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->url(CompanyResource::getUrl('index')),

            Stat::make(' ', Company::where('status', 2)->count())
                ->description('Activos')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(CompanyResource::getUrl('index') . '?tab=aprobados'),
            
            Stat::make(' ', Company::where('status', 3)->count())
                ->description('Rechazadas')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->url(CompanyResource::getUrl('index') . '?tab=rechazados'),

            Stat::make(' ', Company::count())
                ->description('Total')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('info')
                ->url(CompanyResource::getUrl('index') . '?tab=todos'),
        ];
    }
}
