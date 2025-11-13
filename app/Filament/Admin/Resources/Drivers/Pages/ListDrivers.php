<?php

namespace App\Filament\Admin\Resources\Drivers\Pages;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\Drivers\DriverResource;
use App\Models\Driver;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

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
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [EntityStatusEnum::PENDING_APPROVAL, EntityStatusEnum::DOCUMENT_REVIEW]))
                ->badge(fn () => Driver::whereIn('status', [EntityStatusEnum::PENDING_APPROVAL, EntityStatusEnum::DOCUMENT_REVIEW])->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', EntityStatusEnum::ACTIVE))
                ->badge(fn () => Driver::where('status', EntityStatusEnum::ACTIVE)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE, EntityStatusEnum::INFECTED_DOCUMENTS]))
                ->badge(fn () => Driver::whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE, EntityStatusEnum::INFECTED_DOCUMENTS])->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }
}
