<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierDrivers\Pages;

use App\Filament\Admin\Resources\SupplierDrivers\SupplierDriverResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditSupplierDriver extends EditRecord
{
    protected static string $resource = SupplierDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver Documentos'),
            DeleteAction::make(),
        ];
    }
}
