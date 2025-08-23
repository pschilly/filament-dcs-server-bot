<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\PlayerStats;

use Filament\Widgets\ChartWidget;

class PveChart extends ChartWidget
{
    protected $listeners = [
        'serverSelected' => '$refresh',
    ];

    protected ?string $heading = 'PVE Statistics';

    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '225px';

    protected string $color = 'primary';

    public $playerData = [];

    public ?string $selectedModule = null;

    protected function getData(): array
    {
        $kills = $this->playerData['kills'] ?? 0;
        $deaths = $this->playerData['deaths'] ?? 0;
        $kdr = $this->playerData['kdr'] ?? 0;
        $teamkills = $this->playerData['teamkills'] ?? 0;

        return [
            'labels' => ['Kills (PvE)', 'Deaths (PvE)', 'KDR (PvE)', 'Teamkills'],
            'datasets' => [
                [
                    'label' => 'PVP Stats',
                    'data' => [$kills, $deaths, $kdr, $teamkills],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)', // Deaths
                        'rgba(239, 68, 68, 0.5)', // KDR
                        'rgba(245, 158, 66, 0.5)', // Warning
                        'rgba(182, 245, 66, 0.5)', // Teamkills
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 66, 1)',
                        'rgba(182, 245, 66, 1)',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
