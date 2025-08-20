<?php

namespace Pschilly\FilamentDcsServerStats;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Pschilly\FilamentDcsServerStats\Widgets;
use Pschilly\FilamentDcsServerStats\Pages;
use Livewire\Livewire;

class FilamentDcsServerStatsPlugin implements Plugin
{
    /**
     * @var array|null List of enabled pages. If null, all pages are enabled.
     */
    protected ?array $pages = null;

    /**
     * @var bool Whether the Dashboard page is enabled.
     */
    protected bool $dashboard = true;

    /**
     * @var bool Whether to show the ServerSelector Livewire component.
     */
    protected bool $serverSelector = true;

    /**
     * Set which pages to enable.
     * Pass null or empty array to enable all.
     * Pass false to disable all pages.
     */
    public function pages(null|array|false $pages = null): static
    {
        if ($pages === false) {
            $this->pages = [];
        } elseif ($pages && count($pages)) {
            $this->pages = $pages;
        } else {
            $this->pages = null;
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

    public function getId(): string
    {
        return 'filament-dcs-server-stats';
    }

    public function register(Panel $panel): void
    {
        $allPages = [
            Pages\Leaderboard::class,
            // Add other pages here as needed
        ];

        $pagesToRegister = $this->pages === [] ? [] : ($this->pages ?? $allPages);

        // Conditionally add Dashboard page
        if ($this->dashboard && class_exists(Pages\Dashboard::class)) {
            $pagesToRegister[] = Pages\Dashboard::class;
        }

        $panel
            ->pages($pagesToRegister)
            ->widgets([
                Widgets\DailyPlayersChart::class,
                Widgets\TopPilots::class,
                Widgets\TopSquadrons::class,
                Widgets\ServerStatistics::class,
            ]);
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
