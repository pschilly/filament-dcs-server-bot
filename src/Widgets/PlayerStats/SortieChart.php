<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\PlayerStats;

use Carbon\CarbonInterval;
use Filament\Widgets\ChartWidget;

class SortieChart extends ChartWidget
{
    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected(): void
    {
        $this->dispatch('$refresh');
    }

    protected ?string $heading = 'Sortie Statistics';

    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '225px';

    protected string $color = 'gray';

    public $playerData = [];

    public ?string $selectedModule = null;

    public function getFilters(): array
    {
        return [
            'overall' => 'All Time',
            'last_session' => 'Last Session',
        ];
    }

    protected function getData(): array
    {
        $scope = $this->filter ?? 'overall';

        $playtimeSeconds = $this->playerData[$scope]['playtime'] ?? 0;
        $playtimeHours = round(CarbonInterval::seconds($playtimeSeconds)->totalHours);

        $takeoffs = $this->playerData[$scope]['takeoffs'] ?? 0;
        $landings = $this->playerData[$scope]['landings'] ?? 0;
        $ejections = $this->playerData[$scope]['ejections'] ?? 0;
        $crashes = $this->playerData[$scope]['crashes'] ?? 0;

        return [
            'labels' => [
                'Takeoffs',
                'Landings',
                'Ejections',
                'Crashes',
            ],
            'datasets' => [
                [
                    'label' => 'Flight Stats',
                    'data' => [
                        $takeoffs,
                        $landings,
                        $ejections,
                        $crashes,
                    ],
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
        return 'pie';
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
