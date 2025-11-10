<?php

namespace App\Filament\Admin\Resources\Chassis\Pages;

use App\Filament\Admin\Resources\Chassis\ChassisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Chassis;
use App\Enums\ChassisStatusEnum;

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
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [ChassisStatusEnum::PENDING_APPROVAL, ChassisStatusEnum::DOCUMENT_REVIEW]))
                ->badge(fn () => Chassis::whereIn('status', [ChassisStatusEnum::PENDING_APPROVAL, ChassisStatusEnum::DOCUMENT_REVIEW])->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', ChassisStatusEnum::ACTIVE))
                ->badge(fn () => Chassis::where('status', ChassisStatusEnum::ACTIVE)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [ChassisStatusEnum::INACTIVE, ChassisStatusEnum::NEEDS_UPDATE, ChassisStatusEnum::INFECTED_DOCUMENTS]))
                ->badge(fn () => Chassis::whereIn('status', [ChassisStatusEnum::INACTIVE, ChassisStatusEnum::NEEDS_UPDATE, ChassisStatusEnum::INFECTED_DOCUMENTS])->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }
}
