<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class ChassisHeader extends Widget
{
    protected static ?int $sort = 6;

    protected string $view = 'filament.widgets.section-header';

    protected int | string | array $columnSpan = 'full';

    public function getTitle(): string
    {
        return 'Carretas';
    }

    public function getDescription(): string
    {
        return 'Estado de las carretas registradas';
    }
}
