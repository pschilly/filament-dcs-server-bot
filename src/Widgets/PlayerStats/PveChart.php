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


    public function getFilters(): array
    {
        return [
            'overall' => 'All Time',
            'last_session' => 'Last Session'
        ];
    }

    protected function getData(): array
    {
        $scope = $this->filter ?? 'overall';
        $kills = $this->playerData[$scope]['kills'] ?? 0;
        $deaths = $this->playerData[$scope]['deaths'] ?? 0;
        $kdr = $this->playerData[$scope]['kdr'] ?? 0;

        return [
            'labels' => ['Kills (PvE)', 'Deaths (PvE)', 'KDR (PvE)'],
            'datasets' => [
                [
                    'label' => 'PVP Stats',
                    'data' => [$kills, $deaths, $kdr],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)', // Deaths
                        'rgba(239, 68, 68, 0.5)', // KDR
                        'rgba(245, 158, 66, 0.5)', // Warning

                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 66, 1)',

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
