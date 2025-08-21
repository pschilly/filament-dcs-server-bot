<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\Leaderboard;

use Filament\Widgets\Widget;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

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

    // public function mount($serverName = null)
    // {
    //     $this->serverName = $serverName;
    //     $this->updatePodium();
    // }

    // public function updatePodium()
    // {
    //     $apiResponse = DcsServerBotApi::getLeaderboard(what: $this->what, order: $this->order, server_name: $this->serverName)['items'];

    //     $response = [];
    //     if (is_array($apiResponse)) {
    //         if (isset($apiResponse['highscores']) && is_array($apiResponse['highscores'])) {
    //             $response = $apiResponse['highscores'];
    //         } else {
    //             $response = $apiResponse;
    //         }
    //     }

    //     $this->first = $response[0] ?? null;
    //     $this->second = $response[1] ?? null;
    //     $this->third = $response[2] ?? null;
    // }
}
