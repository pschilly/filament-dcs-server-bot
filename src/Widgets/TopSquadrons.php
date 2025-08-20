<?php

namespace Pschilly\FilamentDcsServerStats\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Http;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class TopSquadrons extends ChartWidget
{
    protected ?string $heading = 'Top Squadrons by Credits';

    protected ?string $pollingInterval = '120s';

    protected array | string | int $columnSpan = 2;

    protected ?string $maxHeight = '300px';

    public ?string $filter = '3';

    protected function getFilters(): ?array
    {
        return [
            '3' => '3 Squadrons',
            '5' => '5 Squadrons',
            '10' => '10 Squadrons',
            '15' => '15 Squadrons',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = (int) $this->filter;

        // Get all squadrons
        $squadrons = DcsServerBotApi::getSquadronList();

        $squadronCredits = [];

        // For each squadron, get credits
        foreach ($squadrons as $squadron) {
            $name = $squadron['name'] ?? null;
            if (! $name) {
                continue;
            }

            $creditsResponse = DcsServerBotApi::getSquadronCredits($name);
            $credits = $creditsResponse['credits'] ?? 0;
            $squadronCredits[$name] = $credits;
        }

        // Sort squadrons by credits descending
        arsort($squadronCredits);

        // Take top N squadrons based on filter
        $topSquadrons = array_slice($squadronCredits, 0, $activeFilter, true);

        return [
            'datasets' => [
                [
                    'label' => 'Credits',
                    'data' => array_values($topSquadrons),
                    'fill' => true,
                ],
            ],
            'labels' => array_keys($topSquadrons),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
