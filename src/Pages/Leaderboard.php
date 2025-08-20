<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Pschilly\DcsServerBotApi\DcsServerBotApi;
use Pschilly\FilamentDcsServerStats\Widgets;

class Leaderboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament-dcs-server-stats::pages.leaderboard.index';

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

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getHeaderWidgets(): array
    {
        return [
            Widgets\Leaderboard\Podium::class,
        ];
    }

    public function table(Table $table): Table
    {

        return $table
            ->records(function (int $page, int $recordsPerPage): LengthAwarePaginator {
                $skip = ($page - 1) * $recordsPerPage;

                $response = DcsServerBotApi::getTopKills(server_name: $this->serverName, limit: $recordsPerPage, offset: $skip, returnType: 'collection');

                return new LengthAwarePaginator(
                    items: $response->toArray(),
                    total: $response->count(),
                    perPage: $recordsPerPage,
                    currentPage: $page
                );
            })
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No.')
                    ->view('filament-dcs-server-stats::tables.columns.leaderboard-row-number'),
                TextColumn::make('nick')->label('Callsign'),
                TextColumn::make('kills')->label('Kills'),
                TextColumn::make('deaths')->label('Deaths'),
                TextColumn::make('kdr')->label('KDR')->numeric(2),

            ]);
    }
}
