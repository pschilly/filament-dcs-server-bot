<?php

namespace Pschilly\DcsServerBotApi\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Pschilly\DcsServerBotApi\DcsServerBotApi;
use Pschilly\DcsServerBotApi\Traits\ServerSpecificResults;

class DailyPlayersChart extends ChartWidget
{
    use ServerSpecificResults;

    protected ?string $pollingInterval = '120s';

    protected $listeners = ['serverSelected' => 'handleServerSelected'];

    protected ?string $heading = 'Daily Players Chart';

    protected array | string | int $columnSpan = 4;

    protected ?string $maxHeight = '300px';

    protected string $color = 'primary';

    protected function getData(): array
    {
        $data = DcsServerBotApi::getServerStats($this->serverName);

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
