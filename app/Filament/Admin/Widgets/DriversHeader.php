<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class DriversHeader extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.section-header';

    protected int | string | array $columnSpan = 'full';

    public function getTitle(): string
    {
        return 'Conductores';
    }

    public function getDescription(): string
    {
        return 'Estado de los conductores registrados';
    }
}
