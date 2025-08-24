<?php

namespace Pschilly\FilamentDcsServerStats\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Pschilly\DcsServerBotApi\DcsServerBotApi;
use Pschilly\FilamentDcsServerStats\Traits\ServerSpecificResults;

class DailyPlayersChart extends ChartWidget
{
    use ServerSpecificResults;

    protected ?string $pollingInterval = '120s';

    protected $listeners = ['serverSelected' => 'handleServerSelected'];

    protected ?string $heading = 'Daily Players Chart';

    // protected ?string $heading = 'Top Pilots';
    protected int | string | array $columnSpan = [
        'sm' => 4,
        'lg' => 4,
    ];

    protected ?string $maxHeight = '300px';

    protected string $color = 'primary';

    protected function getData(): array
    {
        $serverName = $this->serverName;
        $cacheName = Str::slug($serverName) . '_serverStatistics';
        $cacheKey = "dcsstats_$cacheName";

        $data = Cache::remember($cacheKey, now()->addHours(1), function () use ($serverName) {
            return DcsServerBotApi::getServerStats($serverName);
        });

        $labels = [];
        $values = [];

        foreach ($data['daily_players'] ?? [] as $entry) {
            $labels[] = Carbon::parse($entry['date'])->format('d M y'); // e.g., 08 Aug 25
            $values[] = $entry['player_count'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Players',
                    'data' => $values,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
