<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\Leaderboard;

use Filament\Widgets\Widget;

class Podium extends Widget
{
    public $first;

    public $second;

    public $third;

    public $what = 'kills';

    public $serverName = null;

    protected string $view = 'filament-dcs-server-stats::pages.leaderboard.widgets.podium';

    protected $listeners = [
        'filterUpdate' => '$refresh',
    ];
}
