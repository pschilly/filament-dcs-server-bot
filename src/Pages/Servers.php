<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Servers extends Page
{
    protected static BackedEnum | string | null $navigationIcon = Heroicon::ServerStack;

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
