<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class TrucksHeader extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.section-header';

    protected int | string | array $columnSpan = 'full';

    public function getTitle(): string
    {
        return 'Tractos';
    }

    public function getDescription(): string
    {
        return 'Estado de los tractos registrados';
    }
}
