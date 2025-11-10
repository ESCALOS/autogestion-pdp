<?php

namespace App\Filament\Widgets;

use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Empresas Pendientes', Company::where('status', 1)->count())
                ->description('En espera de validación')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->url(CompanyResource::getUrl('index')),

            Stat::make('Empresas Aprobadas', Company::where('status', 2)->count())
                ->description('Documentos validados')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([3, 5, 7, 9, 11, 13, 15, 17])
                ->url(CompanyResource::getUrl('index') . '?tab=aprobados'),

            Stat::make('Empresas Rechazadas', Company::where('status', 3)->count())
                ->description('Requieren correcciones')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->chart([1, 2, 1, 3, 2, 4, 3, 2])
                ->url(CompanyResource::getUrl('index') . '?tab=rechazados'),

            Stat::make('Total de Empresas', Company::count())
                ->description('Empresas registradas')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary')
                ->chart([5, 10, 12, 17, 19, 20, 23, 22])
                ->url(CompanyResource::getUrl('index') . '?tab=todos'),
        ];
    }
}
