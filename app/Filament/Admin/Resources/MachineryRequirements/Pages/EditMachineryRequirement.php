<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MachineryRequirements\Pages;

use App\Filament\Admin\Resources\MachineryRequirements\MachineryRequirementResource;
use Filament\Resources\Pages\EditRecord;

final class EditMachineryRequirement extends EditRecord
{
    protected static string $resource = MachineryRequirementResource::class;
}
