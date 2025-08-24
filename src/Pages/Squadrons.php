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

    public array $squadrons = [];

    public function mount(?string $squadronName = null): void
    {
        $this->squadrons = $this->getSquadrons();
    }

    public static function getSquadrons(): array
    {
        // Fetch the squadrons from the API or any other source
        return DcsServerBotApi::getSquadronList();
    }
}
