<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\PlayerStats;

use Filament\Widgets\ChartWidget;

class PvpChart extends ChartWidget
{
    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected(): void
    {
        $this->dispatch('$refresh');
    }

    protected ?string $heading = 'PVP Statistics';

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
        $kills = $this->playerData[$scope]['kills_pvp'] ?? 0;
        $deaths = $this->playerData[$scope]['deaths_pvp'] ?? 0;
        $kdr = $this->playerData[$scope]['kdr_pvp'] ?? 0;

        return [
            'labels' => ['Kills (PvP)', 'Deaths (PvP)', 'KDR (PvP)'],
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
