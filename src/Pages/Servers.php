<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use App\Concerns\CanBeToggledResponsively;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Pschilly\DcsServerBotApi\DcsServerBotApi;
use Filament\Actions\Action;

class Servers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum | string | null $navigationIcon = Heroicon::ServerStack;

    protected string $view = 'filament-dcs-server-stats::pages.servers.index';

    public ?array $servers = [];

    public $serverStatusIcon = [
        'Running' => 'heroicon-s-play',
        'Paused' => 'heroicon-s-pause',
        'Stopped' => 'heroicon-s-stop',
        'Shutdown' => 'heroicon-s-stop'
    ];


    public function mount(): void
    {
        $this->servers = $this->getServers();
    }

    public static function getServers(): array
    {
        return DcsServerBotApi::getServerList();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn(): array => $this->servers ?? [])
            ->columns([
                TextColumn::make('name')
                    ->label('Server Name')
                    ->icon(fn($record): Heroicon => match ($record['status']) {
                        'Shutdown' => Heroicon::OutlinedServer,
                        default => Heroicon::Server,
                    })
                    ->iconColor(
                        fn($record): string => match ($record['status']) {
                            'Running' => 'success',
                            'Paused' => 'warning',
                            'Stopped' => 'danger',
                            default => 'gray',
                        }
                    ),
                TextColumn::make('address')
                    ->label('Address')
                    ->visibleFrom('md'),
                TextColumn::make('mission.name')
                    ->label('Mission')
                    ->grow()
                    ->visibleFrom('md'),
                TextColumn::make('mission.theatre')
                    ->label('Theatre')
                    ->visibleFrom('md'),
                TextColumn::make('mission.blue_slots')
                    ->badge()
                    ->color('info')
                    ->icon('pilot')
                    ->label('Blue Slots')
                    ->formatStateUsing(fn($record) => $record['mission']['blue_slots_used'] . ' | ' . $record['mission']['blue_slots'])
                    ->visibleFrom('md'),
                TextColumn::make('mission.red_slots')
                    ->badge()
                    ->color('danger')
                    ->icon('pilot')
                    ->label('Red Slots')
                    ->formatStateUsing(fn($record) => $record['mission']['red_slots_used'] . ' | ' . $record['mission']['red_slots'])
                    ->visibleFrom('md'),
                TextColumn::make('password')
                    ->label('Access')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record['password'] ? 'Private' : 'Public')
                    ->color(fn(string $state): string => (!$state) ? 'info' : 'warning')
                    ->icon(fn($state) => (!$state) ? 'heroicon-s-lock-open' : 'heroicon-s-lock-closed'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Running' => 'success',
                        'Paused' => 'warning',
                        'Stopped' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn($record): string => $this->serverStatusIcon[$record['status']] ?? 'heroicon-o-question-mark-circle')
            ]);
    }
}
