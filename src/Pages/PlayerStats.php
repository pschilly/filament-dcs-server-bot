<?php

namespace Pschilly\FilamentDcsServerStats\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class PlayerStats extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum | string | null $navigationIcon = Heroicon::ChartBar;

    protected string $view = 'filament-dcs-server-stats::pages.playerstats.index';

    public ?string $serverName = null;

    public string $nick = '';

    public array $playerData = [];

    public bool $showForm = true;

    public string $tab = 'lifetime-statistics';

    protected $listeners = [
        'serverSelected' => 'handleServerSelected',
        'restorePlayerFromSession' => 'restorePlayerFromSession',
    ];

    public function handleServerSelected($serverName): void
    {
        $this->serverName = $serverName;
    }

    public function restorePlayerFromSession(string $nick): void
    {
        if (! is_string($nick) || trim($nick) === '') {
            return;
        }
        $this->loadPlayer($nick);
    }

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Select::make('nick')
                ->label('Player Callsign')
                ->placeholder('Search for a player...')
                ->searchable()
                ->columnSpanFull()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (?string $state) {
                    $value = trim((string) ($state ?? ''));
                    if ($value === '') {
                        return;
                    }
                    $this->loadPlayer($value);
                })
                ->getSearchResultsUsing(function (?string $search) {
                    $search = trim((string) ($search ?? ''));
                    if (strlen($search) < 3) {
                        return [];
                    }

                    try {
                        $results = \Pschilly\DcsServerBotApi\DcsServerBotApi::getUser($search);

                        if (empty($results)) {
                            return [];
                        }

                        return collect($results)
                            ->mapWithKeys(fn ($item) => [
                                (string) ($item['nick'] ?? $item[0] ?? '') => (string) ($item['nick'] ?? $item[0] ?? ''),
                            ])
                            ->filter(fn ($label, $value) => $value !== '')
                            ->toArray();
                    } catch (\Throwable $e) {
                        logger()->error('Player search failed', ['query' => $search, 'error' => $e->getMessage()]);

                        return [];
                    }
                }),
        ];
    }

    public function mount(): void
    {
        // initialise the form state from public properties
        $this->form->fill([
            'nick' => $this->nick,
        ]);
    }

    public function performSearch(): void
    {
        $state = $this->form->getState();
        $this->nick = trim((string) ($state['nick'] ?? ''));

        if ($this->nick === '') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Enter a player callsign.']);

            return;
        }

        try {
            $result = DcsServerBotApi::getUser($this->nick);

            if (is_null($result)) {
                $this->playerData = [];
            } elseif (is_array($result)) {
                $this->playerData = $result;
            } else {
                $this->playerData = collect($result)->toArray();
            }

            $this->showForm = false;
            logger()->info('PlayerStats: loaded player', ['nick' => $this->nick, 'count' => count($this->playerData)]);
        } catch (\Throwable $e) {
            logger()->error('PlayerStats: failed to load player', ['nick' => $this->nick, 'error' => $e->getMessage()]);
            $this->playerData = [];
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Failed to load player.']);
        }
    }

    // Add this helper to load a player and show the details section
    // make public so filaments closure can call it
    public function loadPlayer(string $identifier): void
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return;
        }

        // Hide the form immediately so the details panel can show a loading state
        $this->showForm = false;

        try {
            // Find the User
            $result = DcsServerBotApi::getUser($identifier);

            $this->playerData = DcsServerBotApi::getPlayerInfo($this->serverName, $result[0]['nick'], $result[0]['date']);

            // sync form and state
            $this->nick = $identifier;
            $this->form->fill(['nick' => $this->nick]);

            // Persist selection in sessionStorage via Livewire dispatch (client listens with Livewire.on)
            $this->dispatch('dcs_stats:setSessionPlayer', $this->nick);

            logger()->info('PlayerStats: loaded player', ['nick' => $this->nick, 'count' => count($this->playerData)]);
        } catch (\Throwable $e) {
            logger()->error('PlayerStats: failed to load player', ['nick' => $identifier, 'error' => $e->getMessage()]);
            $this->playerData = [];
            // show form again if load failed
            $this->showForm = true;

            Notification::make()
                ->danger()
                ->title('Failed to load player.')
                ->send();
        }
    }

    public function clearSelection(): void
    {
        $this->nick = '';
        $this->playerData = [];
        $this->showForm = true;
        $this->form->fill(['nick' => '']);

        // Tell frontend to clear sessionStorage via Livewire dispatch
        $this->dispatch('dcs_stats:clearSessionPlayer');
    }

    // Return dynamic page title: "<name>'s Statistics" when a player is selected
    public function getTitle(): string
    {
        return '';
    }

    // Add a header action for "Change Player"
    protected function getHeaderActions(): array
    {
        return [
            // Action::make('changePlayer')
            //     ->label('Change Player')
            //     ->action('clearSelection')
            //     ->icon(Heroicon::ChevronDoubleLeft)
            //     ->visible(fn(): bool => !$this->showForm),
        ];
    }
}
