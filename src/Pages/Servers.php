<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Page;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class Servers extends Page
{

    protected string $view = 'filament-dcs-server-stats::pages.servers.index';

    public $serverName = null;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
    }
}
