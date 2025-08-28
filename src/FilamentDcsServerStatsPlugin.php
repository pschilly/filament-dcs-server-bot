<?php

namespace Pschilly\FilamentDcsServerStats;

use Filament\Contracts\Plugin;
use Filament\Pages\Page;
use Filament\Panel;
use Livewire\Livewire;

class FilamentDcsServerStatsPlugin implements Plugin
{
    /**
     * @var array|null List of enabled pages. If null, all pages are enabled.
     */
    protected ?array $pages = null;

    /**
     * @var array|null List of enabled dashboard widgets. If null, all widgets are enabled.
     */
    protected ?array $dashboardWidgets = null;

    /**
     * @var bool Whether the Dashboard page is enabled.
     */
    protected bool $dashboard = true;

    /**
     * @var bool Whether to show the ServerSelector Livewire component.
     */
    protected bool $serverSelector = true;

    /**
     * @var array|null List of columns to show on the Leaderboard page.
     */
    protected ?array $leaderboardColumns = null;

    /**
     * @var array|null List of enabled player stats widgets. If null, all widgets are enabled.
     */
    protected ?array $playerStatsWidgets = null;

    /**
     * Set which pages to enable.
     * Pass null or empty array to enable all.
     * Pass false to disable all pages.
     */
    public function pages(null | array | false $pages = null): static
    {
        if ($pages === false) {
            $this->pages = [];
        } elseif (is_array($pages) && count($pages) === 0) {
            $this->pages = [];
        } elseif (is_array($pages)) {
            $this->pages = $pages;
        } else {
            $this->pages = null;
        }

        return $this;
    }

    /**
     * Set which widgets to enable.
     * Pass null or empty array to enable all.
     * Pass false to disable all widgets.
     */
    public function dashboardWidgets(null | array | false $dashboardWidgets = null): static
    {
        if ($dashboardWidgets === false) {
            $this->dashboardWidgets = [];
        } elseif (is_array($dashboardWidgets) && count($dashboardWidgets) === 0) {
            $this->dashboardWidgets = [];
        } elseif (is_array($dashboardWidgets)) {
            $this->dashboardWidgets = $dashboardWidgets;
        } else {
            $this->dashboardWidgets = null;
        }

        return $this;
    }

    /**
     * Set which widgets to enable.
     * Pass null or empty array to enable all.
     * Pass false to disable all widgets.
     */
    public function playerStatsWidgets(null | array | false $playerStatsWidgets = null): static
    {
        if ($playerStatsWidgets === false) {
            $this->playerStatsWidgets = [];
        } elseif (is_array($playerStatsWidgets) && count($playerStatsWidgets) === 0) {
            $this->playerStatsWidgets = [];
        } elseif (is_array($playerStatsWidgets)) {
            $this->playerStatsWidgets = $playerStatsWidgets;
        } else {
            $this->playerStatsWidgets = null;
        }

        return $this;
    }

    /**
     * Enable or disable the Dashboard page.
     * Pass false to disable, or omit to enable.
     */
    public function dashboard(bool $enabled = true): static
    {
        $this->dashboard = $enabled;

        return $this;
    }

    /**
     * Enable or disable the ServerSelector Livewire component.
     * Pass false to disable, or omit to enable.
     */
    public function serverSelector(bool $enabled = true): static
    {
        $this->serverSelector = $enabled;

        return $this;
    }

    /**
     * Set which columns to show on the Leaderboard page.
     * Pass an array of column keys.
     */
    public function leaderboardColumns(?array $columns = null): static
    {
        $this->leaderboardColumns = $columns;

        return $this;
    }

    /**
     * Get the configured leaderboard columns, or defaults.
     */
    public function getLeaderboardColumns(): array
    {
        return $this->leaderboardColumns ?? [
            'kills',
            'deaths',
            'kdr',
            'credits',
            'playtime',
        ];
    }

    public function getId(): string
    {
        return 'filament-dcs-server-stats';
    }

    public function register(Panel $panel): void
    {
        $allPages = [
            Pages\Leaderboard::class,
            Pages\PlayerStats::class,
            Pages\Squadrons::class,
            Pages\Servers::class,
        ];

        $pagesToRegister = $this->pages === [] ? [] : ($this->pages ?? $allPages);

        $allWidgets = [
            Widgets\ServerStatistics::class,
            Widgets\DailyPlayersChart::class,
            Widgets\TopPilots::class,
            Widgets\TopSquadrons::class,
        ];

        $widgetsToRegister = $this->dashboardWidgets === [] ? [] : ($this->dashboardWidgets ?? $allWidgets);

        // Only add Dashboard if not disabling all pages
        if ($this->dashboard && class_exists(Pages\Dashboard::class) && $pagesToRegister !== []) {
            $pagesToRegister[] = Pages\Dashboard::class;
        }

        $panel
            ->pages($pagesToRegister)
            ->widgets($widgetsToRegister);
    }

    public function boot(Panel $panel): void
    {
        // Register the Livewire component if needed
        Livewire::component('serverselector', \Pschilly\FilamentDcsServerStats\Livewire\ServerSelector::class);

        if ($this->serverSelector) {
            \Filament\Support\Facades\FilamentView::registerRenderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn(): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'serverselector\')'),
            );
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
