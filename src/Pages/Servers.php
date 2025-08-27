<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class Servers extends Page
{
    protected static BackedEnum | string | null $navigationIcon = Heroicon::ServerStack;

    protected string $view = 'filament-dcs-server-stats::pages.servers.index';

    public ?array $servers = [];

    public function mount(): void
    {
        $this->servers = $this->getServers();
    }

    public static function getServers(): array
    {
        return DcsServerBotApi::getServerList();
    }
}
