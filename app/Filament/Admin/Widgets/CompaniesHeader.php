<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class CompaniesHeader extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.section-header';

    protected int | string | array $columnSpan = 'full';

    public function getTitle(): string
    {
        return 'Empresas';
    }

    public function getDescription(): string
    {
        return 'Estado de las empresas registradas';
    }
}
