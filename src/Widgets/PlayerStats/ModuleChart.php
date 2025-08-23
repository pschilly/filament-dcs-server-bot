<?php

namespace Pschilly\FilamentDcsServerStats\Widgets\PlayerStats;

use Filament\Widgets\ChartWidget;
use Pschilly\FilamentDcsServerStats\Traits\HasCleanAircraftNames;

class ModuleChart extends ChartWidget
{
    use HasCleanAircraftNames;

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected(): void
    {
        $this->dispatch('$refresh');
    }

    protected ?string $heading = 'Airframe Statistics';

    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '225px';

    protected string $color = 'primary';

    public $playerData = [];

    public ?string $selectedModule = null;

    protected function getData(): array
    {
        $stats = $this->playerData['module_stats'] ?? [];

        // Use the filter value (note: filters, not filter)
        $selectedModule = $this->filter ?? null;

        // If no module selected, use the first module
        if (! $selectedModule && count($stats)) {
            $selectedModule = $stats[0]['module'] ?? null;
        }

        if ($selectedModule) {
            $stat = collect($stats)->first(fn ($stat) => ($stat['module'] ?? '') === $selectedModule);

            if ($stat) {
                return [
                    'datasets' => [
                        [
                            'label' => $stat['module'] ?? 'Unknown',
                            'data' => [
                                $stat['kills'] ?? 0,
                                $stat['deaths'] ?? 0,
                                $stat['kdr'] ?? 0,
                            ],
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
                    'labels' => ['Kills', 'Deaths', 'KDR'],
                ];
            }
        }

        // No matching module, show empty chart
        return [
            'datasets' => [],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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

    public function getFilters(): array
    {
        $modules = collect($this->playerData['module_stats'] ?? [])
            ->pluck('module')
            ->unique()
            ->filter()
            ->values()
            ->all();

        return collect($modules)->mapWithKeys(function ($module) {
            return [$module => $this->getCleanAircraftName($module)];
        })->toArray();
    }

    public function getHeading(): ?string
    {
        // $stats = $this->playerData['module_stats'] ?? [];
        // $selectedModule = $this->filter ?? null;

        // if (!$selectedModule && count($stats)) {
        //     $selectedModule = $stats[0]['module'] ?? null;
        // }

        // if ($selectedModule) {
        //     $cleanName = $this->getCleanAircraftName($selectedModule);
        //     return $cleanName . ' Statistics';
        // }

        return 'Airframe Statistics';
    }
}
