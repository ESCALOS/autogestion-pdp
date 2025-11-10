<?php

namespace App\Filament\Admin\Resources\Chassis\Pages;

use App\Filament\Admin\Resources\Chassis\ChassisResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChassis extends EditRecord
{
    protected static string $resource = ChassisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
