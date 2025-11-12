<?php

namespace App\Filament\Admin\Resources\Chassis\Pages;

use App\Filament\Admin\Resources\Chassis\ChassisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Chassis;
use App\Enums\EntityStatusEnum;

class ListChassis extends ListRecords
{
    protected static string $resource = ChassisResource::class;

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
                ->badge(fn () => Chassis::whereIn('status', [EntityStatusEnum::PENDING_APPROVAL, EntityStatusEnum::DOCUMENT_REVIEW])->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', EntityStatusEnum::ACTIVE))
                ->badge(fn () => Chassis::where('status', EntityStatusEnum::ACTIVE)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE, EntityStatusEnum::INFECTED_DOCUMENTS]))
                ->badge(fn () => Chassis::whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE, EntityStatusEnum::INFECTED_DOCUMENTS])->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }
}
