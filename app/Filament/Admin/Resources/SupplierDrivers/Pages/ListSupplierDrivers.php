<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierDrivers\Pages;

use App\Enums\EntityStatusEnum;
use App\Filament\Admin\Resources\SupplierDrivers\SupplierDriverResource;
use App\Models\SupplierDriver;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

final class ListSupplierDrivers extends ListRecords
{
    protected static string $resource = SupplierDriverResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),

            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [EntityStatusEnum::PENDING_APPROVAL]))
                ->badge(fn () => SupplierDriver::whereIn('status', [EntityStatusEnum::PENDING_APPROVAL])->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', EntityStatusEnum::ACTIVE))
                ->badge(fn () => SupplierDriver::where('status', EntityStatusEnum::ACTIVE)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE]))
                ->badge(fn () => SupplierDriver::whereIn('status', [EntityStatusEnum::INACTIVE, EntityStatusEnum::NEEDS_UPDATE])->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pendientes';
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
