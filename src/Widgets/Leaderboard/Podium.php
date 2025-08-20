<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\Leaderboard;

use Filament\Widgets\Widget;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class Podium extends Widget
{
    public $first = null;
    public $second = null;
    public $third = null;

    protected string $view = 'filament-dcs-server-stats::pages.leaderboard.widgets.podium';

    public function mount($serverName = null)
    {
        // Fetch top 3 from the highscore endpoint
        $response = DcsServerBotApi::getTopKills(3, $serverName);
        $this->first  = $response[0] ?? null;
        $this->second = $response[1] ?? null;
        $this->third  = $response[2] ?? null;
    }
}
