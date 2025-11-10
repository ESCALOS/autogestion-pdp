<?php

namespace App\Filament\Admin\Resources\Trucks\Pages;

use App\Filament\Admin\Resources\Trucks\TruckResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTruck extends EditRecord
{
    protected static string $resource = TruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
