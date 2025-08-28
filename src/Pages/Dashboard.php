<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Pschilly\FilamentDcsServerStats\Widgets;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getColumns(): int | array
    {
        return [
            'sm' => 1, // Use 1 column on small screens and up
            'md' => 2, // Use 2 columns on medium screens and up
            'lg' => 4, // Use 3 columns on large screens and up
        ];
    }

    public function getWidgets(): array
    {
        // Get the current panel instance
        $panel = filament()->getCurrentPanel();

        // Return the widgets registered on the panel
        return $panel->getWidgets();
    }
}
