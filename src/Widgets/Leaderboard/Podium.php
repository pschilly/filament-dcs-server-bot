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
    public $what = 'deaths';
    public $order = 'asc';

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
        'leaderboardSortColumn' => 'handleLeaderboardSortColumn',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
        $this->updatePodium();
    }
    public function handleLeaderboardSortColumn($sort)
    {
        $this->what = $sort['column'];
        $this->order = $sort['direction'];
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
        $apiResponse = DcsServerBotApi::getLeaderboard(what: $this->what, order: $this->order, server_name: $this->serverName)['items'];

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
