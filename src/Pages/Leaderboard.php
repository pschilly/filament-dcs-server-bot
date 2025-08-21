<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
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
            ->records(function (
                ?string $sortColumn,
                ?string $sortDirection,
                ?string $search,
                int $page,
                int $recordsPerPage
            ): LengthAwarePaginator {
                $skip = ($page - 1) * $recordsPerPage;

                $params = [
                    'limit' => $recordsPerPage,
                    'skip' => $skip,
                ];


                if ($sortColumn) {
                    $params['what'] = $sortColumn ?? 'kills';
                    $params['order'] = $sortDirection ?? 'desc';
                    $this->dispatch('leaderboardSortColumn', ['column' => $sortColumn, 'direction' => $sortDirection]);
                } else {
                    $this->dispatch('leaderboardSortColumn', ['column' => 'kills', 'direction' => 'desc']);
                }

                if (filled($search)) {
                    $params['q'] = $search;
                }


                $response = DcsServerBotApi::getLeaderboard(
                    what: $params['what'] ?? 'kills',
                    order: $params['order'] ?? 'desc',
                    query: (isset($params['q']) ? $params['q'] : null),
                    limit: $params['limit'],
                    offset: $params['skip'],
                    server_name: $this->serverName,
                    returnType: 'collection'
                );


                return new LengthAwarePaginator(
                    items: $response['items'],
                    total: $response['total_count'],
                    perPage: $recordsPerPage,
                    currentPage: $page
                );
            })
            ->columns([
                TextColumn::make('row_num')
                    ->label('No.')
                    ->view('filament-dcs-server-stats::tables.columns.leaderboard-row-number'),
                TextColumn::make('nick')->label('Callsign')->searchable(),
                TextColumn::make('kills')->label('Kills')->sortable(),
                TextColumn::make('deaths')->label('Deaths')->sortable(),
                TextColumn::make('kdr')->label('KDR')->numeric(2)->sortable(),

            ])
            ->striped()
            ->searchable()
            ->persistSortInSession()
            ->defaultSort('kills', direction: 'desc');
    }
}
