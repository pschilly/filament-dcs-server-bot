<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use BackedEnum;
use Carbon\CarbonInterval;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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

    protected static BackedEnum | string | null $navigationIcon = Heroicon::Trophy;

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
        $cacheKey = "leaderboard_$cacheName";
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

    public function leaderboardColumns()
    {
        // Get the configured columns from the plugin
        $pluginColumnsRaw = filament('filament-dcs-server-stats')->getLeaderboardColumns();
        $pluginColumns = collect($pluginColumnsRaw)->pluck('column-name')->all();

        $columnMap = [
            'rank' => TextColumn::make('rank')
                ->label('No.')
                ->view('filament-dcs-server-stats::tables.columns.leaderboard-row-number'),
            'nick' => TextColumn::make('nick')->label('Callsign')->searchable(),
            'kills' => TextColumn::make('kills')->label('Kills')->sortable(),
            'deaths' => TextColumn::make('deaths')->label('Deaths')->sortable(),
            'kdr' => TextColumn::make('kdr')->label('KDR')->numeric(2)->sortable(),
            'credits' => TextColumn::make('credits')->label('Credits')->sortable(),
            'playtime' => TextColumn::make('playtime')
                ->label('Play Time')
                ->formatStateUsing(fn($state) => \Carbon\CarbonInterval::seconds(round($state / 60) * 60)->cascade()->forHumans())
                ->sortable(),
        ];

        // Always show rank, nick, kills first
        $columns = [
            $columnMap['rank'],
            $columnMap['nick'],
            $columnMap['kills'],
        ];

        // Add the rest based on plugin config (excluding rank, nick, kills)
        foreach ($pluginColumns as $key) {
            if (isset($columnMap[$key]) && !in_array($key, ['rank', 'nick', 'kills'])) {
                $columns[] = $columnMap[$key];
            }
        }

        return $columns;
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
                $sortBy = $sortColumn ?? 'kills';

                // 1) Create a global ranking based on DESC of the selected column
                $globalRanked = collect($this->allRecords)
                    ->sortByDesc($sortBy)
                    ->values()
                    ->map(function ($item, $i) {
                        $item['rank'] = $i + 1; // absolute rank (1 = highest)

                        return $item;
                    });

                // 2) For the table, start from the ranked collection
                $filtered = $globalRanked;

                // 3) Apply search (do NOT reassign rank)
                if (filled($search)) {
                    $filtered = $filtered->filter(
                        fn($item) => str_contains(strtolower($item['nick']), strtolower($search))
                    )->values();
                }

                // 4) Apply display sort as the user requested (but keep absolute rank)
                if ($sortColumn) {
                    $filtered = $sortDirection === 'desc'
                        ? $filtered->sortByDesc($sortColumn)->values()
                        : $filtered->sortBy($sortColumn)->values();
                }

                // 5) Paginate the filtered / display-sorted collection
                $total = $filtered->count();
                $items = $filtered->slice(($page - 1) * $recordsPerPage, $recordsPerPage)->values()->all();

                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $recordsPerPage,
                    $page
                );
            })
            ->columns($this->leaderboardColumns())
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
