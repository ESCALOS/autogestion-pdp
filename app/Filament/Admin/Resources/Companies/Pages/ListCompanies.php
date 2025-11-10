<?php

namespace App\Filament\Admin\Resources\Companies\Pages;

use App\Filament\Admin\Resources\Companies\CompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),

            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 1))
                ->badge(fn () => \App\Models\Company::where('status', 1)->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 2))
                ->badge(fn () => \App\Models\Company::where('status', 2)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 3))
                ->badge(fn () => \App\Models\Company::where('status', 3)->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }
}
