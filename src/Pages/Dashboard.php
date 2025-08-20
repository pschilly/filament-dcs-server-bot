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
        return 4;
    }

    public function getWidgets(): array
    {
        return [
            Widgets\ServerStatistics::class,
            Widgets\DailyPlayersChart::class,
            Widgets\TopPilots::class,
            Widgets\TopSquadrons::class,
        ];
    }
}
