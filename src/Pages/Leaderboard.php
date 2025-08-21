<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class Leaderboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament-dcs-server-stats::pages.leaderboard.index';

    public $serverName = null;

    public array $allRecords = [];

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
    ];

    public function handleServerSelected($serverName)
    {
        $this->serverName = $serverName;

        $cacheName = Str::slug($serverName);
        $cacheKey = "lead_$cacheName";
        logger(['section' => 'serverselect', 'cacheKey' => $cacheKey, 'serverName' => $serverName]);
        $response = Cache::remember($cacheKey, now()->addHours(1), function () use ($serverName) {
            return DcsServerBotApi::getLeaderboard(
                what: 'kills',
                order: 'desc',
                limit: 10000,
                offset: 0,
                server_name: $serverName, // <-- use the parameter, not $this->serverName
                returnType: 'collection'
            );
        });

        $this->allRecords = $response['items'] ?? [];

        // Assign absolute rank
        foreach ($this->allRecords as $i => &$item) {
            $item['rank'] = $i + 1;
        }
    }

    public function mount()
    {
        $serverName = $this->serverName ?? '';
        $cacheName = Str::slug($this->serverName);
        $cacheKey = "lead_$cacheName";
        logger(['section' => 'mount', 'cacheKey' => $cacheKey, 'serverName' => $this->serverName]);
        $response = Cache::remember($cacheKey, now()->addHours(1), function () use ($serverName) {
            return DcsServerBotApi::getLeaderboard(
                what: 'kills',
                order: 'desc',
                limit: 10000,
                offset: 0,
                server_name: $serverName,
                returnType: 'collection'
            );
        });

        $this->allRecords = $response['items'] ?? [];

        // Assign absolute rank
        foreach ($this->allRecords as $i => &$item) {
            $item['rank'] = $i + 1;
        }
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
                $filtered = collect($this->allRecords);

                // Search in PHP
                if (filled($search)) {
                    $filtered = $filtered->filter(
                        fn ($item) => str_contains(strtolower($item['nick']), strtolower($search))
                    );
                }

                // Sort
                if ($sortColumn) {
                    $filtered = $sortDirection === 'desc'
                        ? $filtered->sortByDesc($sortColumn)
                        : $filtered->sortBy($sortColumn);
                }

                // Assign rank after sorting
                $filtered = $filtered->values()->map(function ($item, $i) {
                    $item['rank'] = $i + 1;

                    return $item;
                });

                // Paginate
                $total = $filtered->count();
                $items = $filtered->slice(($page - 1) * $recordsPerPage, $recordsPerPage)->values()->all();

                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $recordsPerPage,
                    $page
                );
            })
            ->columns([
                TextColumn::make('rank')
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

    public function getPodiumData(): array
    {
        $sortColumn = $this->table->getSortColumn() ?? 'kills';

        // Always sort all records DESC for podium
        $ranked = collect($this->allRecords)
            ->sortByDesc($sortColumn)
            ->values();

        return [
            'first' => $ranked->get(0),
            'second' => $ranked->get(1),
            'third' => $ranked->get(2),
            'what' => $sortColumn,
        ];
    }
}
