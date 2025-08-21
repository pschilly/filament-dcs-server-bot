<?php

namespace Pschilly\FilamentDcsServerStats\Widgets;

use Carbon\CarbonInterval;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class ServerStatistics extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '120s';

    public $serverName = null;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;
        // If your chart uses polling, it will update automatically.
        // Otherwise, you may need to trigger a refresh:
        $this->dispatch('$refresh');
    }

    protected int | string | array $columnSpan = 4;

    protected function getStats(): array
    {
        $data = DcsServerBotApi::getServerStats($this->serverName);

        $dailyPlayers = [];
        foreach ($data['daily_players'] as $players) {
            $dailyPlayers[] = $players['player_count'];
        }

        $avgPlaytimeSeconds = isset($data['avgPlaytime']) ? round($data['avgPlaytime'] / 60) * 60 : null;

        return [
            Stat::make('Average Sortie Time', (! is_null($avgPlaytimeSeconds)) ? CarbonInterval::seconds($avgPlaytimeSeconds)->cascade()->forHumans() : 'N/A')->description('Average flight time per session'),
            Stat::make('Combat Record', $data['totalKills'] . ' / ' . $data['totalDeaths'])->description('Kills / Deaths'),
            Stat::make('Total Players', $data['totalPlayers'])->description('Unique pilots'),
            Stat::make('Playtime', (! is_null($data['totalPlaytime'])) ? CarbonInterval::hours($data['totalPlaytime'])->cascade()->forHumans() : 'N/A')->description('All time'),

        ];
    }
}
