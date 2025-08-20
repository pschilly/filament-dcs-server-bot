<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\Leaderboard;

use Filament\Widgets\Widget;
use Pschilly\DcsServerBotApi\DcsServerBotApi;
use Pschilly\FilamentDcsServerStats\Traits\ServerSpecificResults;

class Podium extends Widget
{
    public $first;
    public $second;
    public $third;

    public $serverName = null;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
        $this->updatePodium();
    }


    protected string $view = 'filament-dcs-server-stats::pages.leaderboard.widgets.podium';

    public function mount($serverName = null)
    {
        $this->serverName = $serverName;
        $this->updatePodium();
    }

    public function updatePodium()
    {
        $apiResponse = \Pschilly\DcsServerBotApi\DcsServerBotApi::getTopKills(server_name: $this->serverName, limit: 3);

        $response = [];
        if (is_array($apiResponse)) {
            if (isset($apiResponse['highscores']) && is_array($apiResponse['highscores'])) {
                $response = $apiResponse['highscores'];
            } else {
                $response = $apiResponse;
            }
        }

        $this->first  = $response[0] ?? null;
        $this->second = $response[1] ?? null;
        $this->third  = $response[2] ?? null;
    }
}
