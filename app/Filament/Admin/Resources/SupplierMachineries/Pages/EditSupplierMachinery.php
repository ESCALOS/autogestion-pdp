<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SupplierMachineries\Pages;

use App\Filament\Admin\Resources\SupplierMachineries\SupplierMachineryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

final class EditSupplierMachinery extends EditRecord
{
    protected static string $resource = SupplierMachineryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver Documentos'),
            DeleteAction::make(),
        ];
    }
}
