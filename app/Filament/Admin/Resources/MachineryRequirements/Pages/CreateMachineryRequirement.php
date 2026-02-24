<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Pages;

use App\Filament\Admin\Resources\MachineryRequirements\MachineryRequirementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

final class CreateMachineryRequirement extends CreateRecord
{
    protected static string $resource = MachineryRequirementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
