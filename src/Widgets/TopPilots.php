<?php

namespace Pschilly\FilamentDcsServerStats\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class TopPilots extends ChartWidget
{
    use HasFiltersSchema;

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

    public function mount(): void
    {
        // Ensure filters have default values
        $this->filters['chartType'] ??= 'kills';
        $this->filters['pilotCount'] ??= 5;
    }

    protected ?string $pollingInterval = '120s';

    // protected ?string $heading = 'Top Pilots';
    protected int | string | array $columnSpan = [
        'sm' => 4,
        'lg' => 2,
    ];

    protected ?string $maxHeight = '300px';

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('chartType')
                ->label('Show Top')
                ->options([
                    'kills' => 'Top Kills',
                    'kdr' => 'Top KDR',
                ])
                ->default('kills')
                ->live(),
            Select::make('pilotCount')
                ->label('Show Top')
                ->options([
                    3 => '3 Pilots',
                    5 => '5 Pilots',
                    10 => '10 Pilots',
                    15 => '15 Pilots',
                ])
                ->default(5)
                ->live(),
        ]);
    }

    public function getHeading(): string
    {
        $chartType = $this->filters['chartType'] ?? 'kills';
        $pilotCount = $this->filters['pilotCount'] ?? 5;

        if ($chartType === 'kills') {
            return 'Top ' . $pilotCount . ' Pilots by Kills';
        } elseif ($chartType === 'kdr') {
            return 'Top ' . $pilotCount . ' Pilots by KDR';
        } else {
            return 'Top ' . $pilotCount . ' Pilots';
        }
    }

    protected function getData(): array
    {
        $number = $this->filters['pilotCount'] ?? 5;
        $chartType = $this->filters['chartType'] ?? 'kills';

        $serverName = $this->serverName;
        $cacheName = Str::slug($serverName);
        $cacheKey = "leaderboard_$cacheName";
        $response = Cache::remember($cacheKey, now()->addHours(1), function () use ($serverName, $chartType) {
            return DcsServerBotApi::getLeaderboard(
                what: $chartType,
                order: 'desc',
                limit: 100,
                server_name: $serverName,
                returnType: 'json'
            );
        });

        $pilotData = $response['items'] ?? [];

        // Sort by selected chartType
        usort($pilotData, function ($a, $b) use ($chartType) {
            return ($b[$chartType] ?? 0) <=> ($a[$chartType] ?? 0);
        });

        $labels = [];
        $kills = [];
        $kdrs = [];
        $deaths = [];

        foreach (array_slice($pilotData, 0, $number) as $pilot) {
            $labels[] = $pilot['nick'];
            $kills[] = $pilot['kills'] ?? 0;
            $kdrs[] = $pilot['kdr'] ?? 0;
            $deaths[] = $pilot['deaths'] ?? 0;
        }

        // Change dataset order and assign colors based on chartType
        if ($chartType === 'kdr') {
            $datasets = [
                [
                    'label' => 'KDR',
                    'data' => $kdrs,
                    'fill' => true,
                    'backgroundColor' => 'rgba(245, 158, 66, 0.5)', // warning
                    'borderColor' => 'rgba(245, 158, 66, 1)',
                ],
                [
                    'label' => 'Kills',
                    'data' => $kills,
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // primary
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                ],
                [
                    'label' => 'Deaths',
                    'data' => $deaths,
                    'fill' => true,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)', // danger
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                ],
            ];
        } else {
            $datasets = [
                [
                    'label' => 'Kills',
                    'data' => $kills,
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // primary
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                ],
                [
                    'label' => 'KDR',
                    'data' => $kdrs,
                    'fill' => true,
                    'backgroundColor' => 'rgba(245, 158, 66, 0.5)', // warning
                    'borderColor' => 'rgba(245, 158, 66, 1)',
                ],
                [
                    'label' => 'Deaths',
                    'data' => $deaths,
                    'fill' => true,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)', // danger
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                ],
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
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
