<?php

namespace Pschilly\FilamentDcsServerStats\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use User;

class Highscore extends TableWidget
{
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '120s';

    public $servername = NULL;

    protected $listeners = ['serverSelected' => 'handleServerSelected'];

    public function handleServerSelected($serverName)
    {
        if ($serverName == 0) {
            $this->servername = NULL;
        } else {
            $this->servername = $serverName;
        }
    }
    protected function getFormSchema(): array
    {
        return [
            Select::make('statType')
                ->label('Leaderboard')
                ->options([
                    'playtime' => 'Playtime',
                    'Air Targets' => 'Air Targets',
                    'Ships' => 'Ships',
                    'Air Defence' => 'Air Defence',
                    'Ground Targets' => 'Ground Targets',
                    'KD-Ratio' => 'KD Ratio',
                    'PvP-KD-Ratio' => 'PvP KD Ratio',
                    'Most Efficient Killers' => 'Most Efficient Killers',
                    'Most Wasteful Pilots' => 'Most Wasteful Pilots',
                ])
                ->live()
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (array $filters): array {
                // Table Filters

                $serverName = $this->servername;

                $baseUrl = 'http://192.168.50.143:9876';

                // Fetch the data, passing server_name if set
                $params = [
                    'limit' => 10
                ];

                if ($serverName) {
                    $params['server_name'] = $serverName;
                }

                $raw = Http::baseUrl($baseUrl)
                    ->get('highscore', $params)
                    ->json();

                // Pivot the data
                $categories = [
                    'playtime',
                    'Air Targets',
                    'Ships',
                    'Air Defence',
                    'Ground Targets',
                    'KD-Ratio',
                    'PvP-KD-Ratio',
                    'Most Efficient Killers',
                    'Most Wasteful Pilots',
                ];

                $pilots = [];

                foreach ($categories as $cat) {
                    foreach ($raw[$cat] ?? [] as $entry) {
                        $nick = $entry['nick'];
                        if (!isset($pilots[$nick])) {
                            $pilots[$nick] = ['nick' => $nick];
                        }
                        // Use 'playtime' for playtime, 'value' for others
                        $pilots[$nick][$cat] = $entry['playtime'] ?? $entry['value'] ?? null;
                    }
                }

                // Convert to array of rows
                return array_values($pilots);
            })
            ->filters([
                SelectFilter::make('limit')
                    ->label('Top #')
                    ->options([
                        10 => '10',
                        25 => '25',
                        50 => '50',
                        100 => '100',
                    ])
            ])
            ->columns([
                TextColumn::make('nick')->label('Pilot'),
                TextColumn::make('playtime')->label('Playtime')->toggleable(),
                TextColumn::make('Air Targets')->label('Air Targets')->toggleable(),
                TextColumn::make('Ships')->label('Ships')->toggleable(),
                TextColumn::make('Air Defence')->label('Air Defence')->toggleable(),
                TextColumn::make('Ground Targets')->label('Ground Targets')->toggleable(),
                TextColumn::make('KD-Ratio')->label('KD Ratio')->toggleable(),
                TextColumn::make('PvP-KD-Ratio')->label('PvP KD Ratio')->toggleable(),
                TextColumn::make('Most Efficient Killers')->label('Most Efficient Killers')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('Most Wasteful Pilots')->label('Most Wasteful Pilots')->toggleable()->toggledHiddenByDefault(),
            ]);
    }
}
