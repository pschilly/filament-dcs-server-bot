<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\PlayerStats;

use Carbon\CarbonInterval;
use Filament\Widgets\ChartWidget;

class CombatChart extends ChartWidget
{
    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected(): void
    {
        $this->dispatch('$refresh');
    }

    protected ?string $heading = 'Combat Statistics';

    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '225px';

    protected string $color = 'gray';

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

        // Kills
        $killsPlanes = $this->playerData[$scope]['kills_planes'] ?? 0;
        $killsHelicopters = $this->playerData[$scope]['kills_helicopters'] ?? 0;
        $killsShips = $this->playerData[$scope]['kills_ships'] ?? 0;
        $killsSams = $this->playerData[$scope]['kills_sams'] ?? 0;
        $killsGround = $this->playerData[$scope]['kills_ground'] ?? 0;
        $teamkills = $this->playerData[$scope]['teamkills'] ?? 0;

        // Deaths
        $deathsPlanes = $this->playerData[$scope]['deaths_planes'] ?? 0;
        $deathsHelicopters = $this->playerData[$scope]['deaths_helicopters'] ?? 0;
        $deathsShips = $this->playerData[$scope]['deaths_ships'] ?? 0;
        $deathsSams = $this->playerData[$scope]['deaths_sams'] ?? 0;
        $deathsGround = $this->playerData[$scope]['deaths_ground'] ?? 0;

        return [
            'labels' => [
                'Airplanes',
                'Helicopters',
                'Ships',
                'Air Defence',
                'Ground',
                'Teamkills'
            ],
            'datasets' => [
                [
                    'label' => 'Kills',
                    'data' => [
                        $killsPlanes,
                        $killsHelicopters,
                        $killsShips,
                        $killsSams,
                        $killsGround,
                        $teamkills,
                    ],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',   // Planes
                    ],
                    'fill' => true,
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                    ],
                ],
                [
                    'label' => 'Deaths',
                    'data' => [
                        $deathsPlanes,
                        $deathsHelicopters,
                        $deathsShips,
                        $deathsSams,
                        $deathsGround,
                        0
                    ],
                    'fill' => true,
                    'backgroundColor' => [
                        'rgba(220, 38, 38, 0.5)',
                    ],
                    'borderColor' => [
                        'rgba(220, 38, 38, 1)',
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
                    'display' => true,
                ],
            ],
        ];
    }
}
