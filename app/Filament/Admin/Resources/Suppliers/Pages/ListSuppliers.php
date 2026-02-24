<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Suppliers\Pages;

use App\Enums\CompanyStatusEnum;
use App\Filament\Admin\Resources\Suppliers\SupplierResource;
use App\Models\Supplier;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

final class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),

            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn ($query) => $query->where('status', CompanyStatusEnum::PENDIENTE))
                ->badge(fn () => Supplier::where('status', CompanyStatusEnum::PENDIENTE)->count())
                ->badgeColor('warning'),

            'aprobados' => Tab::make('Aprobados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', CompanyStatusEnum::APROBADO))
                ->badge(fn () => Supplier::where('status', CompanyStatusEnum::APROBADO)->count())
                ->badgeColor('success'),

            'rechazados' => Tab::make('Rechazados')
                ->modifyQueryUsing(fn ($query) => $query->where('status', CompanyStatusEnum::RECHAZADO))
                ->badge(fn () => Supplier::where('status', CompanyStatusEnum::RECHAZADO)->count())
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
