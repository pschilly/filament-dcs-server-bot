<?php

namespace Pschilly\DcsServerBotApi\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\Http;

class TopPilots extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $pollingInterval = '120s';

    // protected ?string $heading = 'Top Pilots';
    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '300px';

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('chartType')
                ->label('Show Top')
                ->options([
                    'topkills' => 'Top Kills',
                    'topkdr' => 'Top KDR',
                ])
                ->default('topkills')
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
        if ($this->filters['chartType'] === 'topkills') {
            return 'Top ' . $this->filters['pilotCount'] . ' Pilots by Kills';
        } elseif ($this->filters['chartType'] === 'topkdr') {
            return 'Top ' . $this->filters['pilotCount'] . ' Pilots by KDR';
        } else {
            return 'Top ' . $this->filters['pilotCount'] . ' Pilots';
        }
    }

    protected function getData(): array
    {
        $number = $this->filters['pilotCount'];
        $chartType = $this->filters['chartType'];

        $baseUrl = 'http://192.168.50.143:9876';
        $data = Http::baseUrl($baseUrl)->get('/' . $chartType, ['limit' => $number])->json();

        $topPilots = array_slice($data, 0, $number);

        $labels = [];
        $values = [];

        foreach ($topPilots as $pilot) {
            $labels[] = $pilot['nick'];
            $values[] = $pilot['kills'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kills',
                    'data' => $values,
                    'fill' => true,
                ],
            ],
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
                    'display' => false,
                ],
            ],
        ];
    }
}
