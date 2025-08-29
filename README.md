# DCS Server Stats FilamentPHP Plugin

![Filament 4.x Required](https://img.shields.io/badge/Filament-4.x-FF2D20?style=for-the-badge)
[![DCSServerBotAPI](https://img.shields.io/badge/DCS_Server_Bot_API-0.1-green?style=for-the-badge)](https://github.com/pschilly/dcs-server-bot-api)
[![DCSServerBot](https://img.shields.io/badge/🤖_Requires-DCS_Server_Bot-green?style=for-the-badge)](https://github.com/Special-K-s-Flightsim-Bots/DCSServerBot)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/pschilly/filament-dcs-server-stats/fix-php-code-style-issues.yml?branch=main&style=for-the-badge)

This package is a series of widgets and pages for Filament PHP and is loaded as a plugin. It interfaces with the DCS Server Bot by Special-K via the Laravel Package DCS Server Bot API to obtain statistics gathered from managed Digital Combat Simulator (DCS) servers.

## Installation

You can install the package via composer:

```bash
composer require pschilly/filament-dcs-server-stats
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views to your theme css file or your app's css file if using the standalone packages.

```css
@source '../../../../vendor/pschilly/filament-dcs-server-stats/resources/**/*.blade.php';
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-dcs-server-stats-views"
```

## Usage

Enable the plugin on your panel of choice:

```php
$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
])
```

## Configuration

By default, this plugin assumes you want everything and has everything turned on. So if you pass no variables at all - that is what you will get.

Referencing below, change what suits you best - I would also encourage you to use some sort of DB based config system to make this a more user friendly experience but I will leave that up to you.

### Server Selector

The server selector is enabled by default, however, if you wish to disable it you may do so as follows:

```php
use Pschilly\FilamentDcsServerStats\Pages as DCSServerBotPages;

$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
    ->serverSelector(false)
]);
```

### Pages

By default, all pages will be registered. If you wish to disable some of them, pass an array of pages as follows:

```php
use Pschilly\FilamentDcsServerStats\Pages as DCSServerBotPages;

$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
    ->pages([
        DCSServerBotPages\Leaderboard::class,
        DCSServerBotPages\PlayerStats::class,
        DCSServerBotPages\Squadrons::class, // Requires that you have the `squadrons` feature enabled within the DCS Server Bot
        DCSServerBotPages\Servers::class,
    ]);
])
```

### Dashboard & Dashboard Widgets

By default, the Dashboard for the Filament Panel that you have enabled the plugin on will be overwritten by the included one with all widgets available. If you wish to change this, configure as follows:

```php
use Pschilly\FilamentDcsServerStats\Widgets as DCSServerBotDashboardWidgets;

$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
    ->dashboard(false) // Will disable the included dashboard - note, the following will also not funciton if you do so.
    ->dashboardWidgets([
        DCSServerBotDashboardWidgets\ServerStatistics::class,
        DCSServerBotDashboardWidgets\DailyPlayersChart::class,
        DCSServerBotDashboardWidgets\TopPilots::class,
        DCSServerBotDashboardWidgets\TopSquadrons::class, // Requires that you have the `squadrons` and `credits` features enabled within the DCS Server Bot
    ]);
])
```

> [!NOTE]
> The order that you put the widgets in the array will dictate the order that they appear on the page.

### Leaderboard Columns

By default, the Leaderboard will show all columns - if you want to change this behaviour, configure as follows:

> [!NOTE]
> No matter what you provide, the bare minimum displayed will be: Ranking, Callsign & Kills so as to not entirely break the page.

```php

$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
    ->leaderboardColumns([
        'deaths',
        'kdr',
        'credits',
        'playtime'
    ]);
])
```

> [!NOTE]
> The order that you put the columns in the array will dictate the order that they appear on the page.

### Player Stats Widgets

By default, the Player Stats page will show all widgets - if you want to change this behaviour, configure as follows:

```php
use Pschilly\FilamentDcsServerStats\Widgets\PlayerStats as DCSServerBotPlayerStatsWidgets;

$panel
->plugins([
    \Pschilly\FilamentDcsServerStats\FilamentDcsServerStatsPlugin::make()
    ->playerStatsWidgets([
        DCSServerBotPlayerStatsWidgets\CombatChart::class,
        DCSServerBotPlayerStatsWidgets\ModuleChart::class,
        DCSServerBotPlayerStatsWidgets\PveChart::class,
        DCSServerBotPlayerStatsWidgets\PvpChart::class,
        DCSServerBotPlayerStatsWidgets\SortieChart::class
    ]);
])
```

> [!NOTE]
> The order that you put the widgets in the array will dictate the order that they appear on the page.

### Squadrons & Servers

At present, there are no configuration values for these pages. What you see is what you get.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Schilly](https://github.com/pschilly)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
