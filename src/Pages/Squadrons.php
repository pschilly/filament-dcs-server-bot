<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class Squadrons extends Page
{
    protected static BackedEnum | string | null $navigationIcon = Heroicon::UserGroup;
    protected string $view = 'filament-dcs-server-stats::pages.squadrons.index';

    public $serverName = null;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
    }
}
