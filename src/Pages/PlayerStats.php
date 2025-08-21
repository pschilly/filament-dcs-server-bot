<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Page;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class PlayerStats extends Page
{

    protected string $view = 'filament-dcs-server-stats::pages.playerstats.index';

    public $serverName = null;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
    }
}
