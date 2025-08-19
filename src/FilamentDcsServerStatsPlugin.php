<?php

namespace Pschilly\FilamentDcsServerStats;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Pschilly\DcsServerBotApi\Widgets;
use Pschilly\DcsServerBotApi\Pages;

class FilamentDcsServerStatsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-dcs-server-stats';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Pages\Leaderboard::class,
            ])
            ->widgets([
                Widgets\DailyPlayersChart::class,
                Widgets\TopPilots::class,
                Widgets\TopSquadrons::class,
                Widgets\ServerStatistics::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
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
