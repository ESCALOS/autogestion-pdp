<?php

namespace App\Filament\Admin\Resources\Chassis\Pages;

use App\Filament\Admin\Resources\Chassis\ChassisResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChassis extends ViewRecord
{
    protected static string $resource = ChassisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
