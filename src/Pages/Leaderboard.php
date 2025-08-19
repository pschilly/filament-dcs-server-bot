<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Carbon\CarbonInterval;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class Leaderboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament-dcs-server-stats::pages.leaderboard';

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function table(Table $table): Table
    {
        $baseUrl = 'http://192.168.50.143:9876';

        // Fetch top kills data
        $mainRecords = Http::baseUrl($baseUrl)
            ->get('/topkills')
            ->collect();

        // Fetch highscore data
        $highscore = Http::baseUrl($baseUrl)
            ->get('/highscore')
            ->json();

        // Helper to get stat by nick from a category
        $getStat = function ($category, $nick) use ($highscore) {
            foreach ($highscore[$category] ?? [] as $entry) {
                if ($entry['nick'] === $nick) {
                    // Air Targets, Ships, Air Defence, Ground Targets use 'value'
                    return $entry['value'] ?? 0;
                }
            }
            return 0;
        };

        // For each record, fetch additional player info
        $records = $mainRecords->map(function ($record, $index) use ($baseUrl, $getStat) {
            $playerInfo = Http::asForm()->baseUrl($baseUrl)
                ->post('/stats', [
                    'nick' => $record['nick'],
                    'date' => $record['date'] ?? null,
                ])
                ->json();

            // Get top Module based on # kills
            $topModuleKills = null;
            if (!empty($playerInfo['killsByModule']) && is_array($playerInfo['killsByModule'])) {
                $topModuleKills = collect($playerInfo['killsByModule'])
                    ->sortByDesc('kills')
                    ->first();
            }

            // Get Top module by KDR
            $topModuleKdr = null;
            if (!empty($playerInfo['kdrByModule']) && is_array($playerInfo['kdrByModule'])) {
                $topModuleKdr = collect($playerInfo['kdrByModule'])
                    ->sortByDesc('kdr')
                    ->first();
            }

            return array_merge($record, [
                'index' => $index + 1,
                'crashes' => $playerInfo['crashes'] ?? null,
                'deaths' => $playerInfo['deaths'] ?? null,
                'deaths_pvp' => $playerInfo['deaths_pvp'] ?? null,
                'ejections' => $playerInfo['ejections'] ?? null,
                'kills' => $playerInfo['kills'] ?? $record['kills'] ?? null,
                'kills_pvp' => $playerInfo['kills_pvp'] ?? null,
                'landings' => $playerInfo['landings'] ?? null,
                'takeoffs' => $playerInfo['takeoffs'] ?? null,
                'teamkills' => $playerInfo['teamkills'] ?? null,
                'playtime' => $playerInfo['playtime'] ?? null,
                'topModuleKillsName' => $topModuleKills['module'] ?? null,
                'topModuleKillsCount' => $topModuleKills['kills'] ?? null,
                'topModuleKdrName' => $topModuleKdr['module'] ?? null,
                'topModuleKdrValue' => $topModuleKdr['kdr'] ?? null,
                // Add highscore stats
                'air_targets' => $getStat('Air Targets', $record['nick']),
                'ships' => $getStat('Ships', $record['nick']),
                'air_defence' => $getStat('Air Defence', $record['nick']),
                'ground_targets' => $getStat('Ground Targets', $record['nick']),
            ]);
        })->toArray();

        return $table
            ->records(fn() => $records)
            ->columns([
                ViewColumn::make('index')
                    ->label('No.')
                    ->view('filament-dcs-server-stats::tables.columns.leaderboard-row-number'),
                TextColumn::make('nick')->label('Pilot'),
                TextColumn::make('kills')->label('Kills')->toggleable(),
                TextColumn::make('deaths')->label('Deaths')->toggleable(),
                TextColumn::make('crashes')->label('Crashes')->toggleable(),
                TextColumn::make('ejections')->label('Ejections')->toggleable(),
                TextColumn::make('landings')->label('Landings')->toggleable(),
                TextColumn::make('takeoffs')->label('Takeoffs')->toggleable(),
                TextColumn::make('teamkills')->label('Team Kills')->toggleable(),
                TextColumn::make('kills_pvp')->label('Kills PvP')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('deaths_pvp')->label('Deaths PvP')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('playtime')->label('Playtime')->formatStateUsing(fn($state) => CarbonInterval::seconds($state)->cascade()->forHumans(null, true, 2))->toggleable(),
                TextColumn::make('kdr')->numeric(decimalPlaces: 2)->label('KDR')->toggleable(),
                TextColumn::make('topModuleKillsName')
                    ->formatStateUsing(function ($state, $record) {
                        return $state . ' (' . ($record['topModuleKillsCount'] ?? '-') . ')';
                    })
                    ->label('Top Module (Kills)')->toggleable(),
                TextColumn::make('topModuleKdrName')
                    ->formatStateUsing(function ($state, $record) {
                        return $state . ' (' . ($record['topModuleKdrValue'] ?? '-') . ')';
                    })
                    ->label('Top Module (KDR)')->toggleable(),
                // New columns from /highscore
                TextColumn::make('air_targets')->label('Air Targets')->toggleable(),
                TextColumn::make('ships')->label('Ships')->toggleable(),
                TextColumn::make('air_defence')->label('Air Defence')->toggleable(),
                TextColumn::make('ground_targets')->label('Ground Targets')->toggleable(),
            ]);
    }
}
