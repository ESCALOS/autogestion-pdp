<?php

namespace App\Filament\Admin\Resources\Trucks\Pages;

use App\Filament\Admin\Resources\Trucks\TruckResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTruck extends ViewRecord
{
    protected static string $resource = TruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
