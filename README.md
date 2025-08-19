# This package is a series of widgets, pages and panels for Filament PHP and is loaded as a plugin. It interfaces with the DCS Server Bot API by Special-K to obtain statistics gathered from managed Digital Combat Simulator (DCS) servers.

![Filament 4.x Required](https://img.shields.io/badge/Filament-4.x-FF2D20?style=for-the-badge)
[![DCSServerBotAPI](https://img.shields.io/badge/DCS_Server_Bot_API-0.1-green?style=for-the-badge)](https://github.com/pschilly/dcs-server-bot-api)
[![DCSServerBot](https://img.shields.io/badge/🤖_Requires-DCS_Server_Bot-green?style=for-the-badge)](https://github.com/Special-K-s-Flightsim-Bots/DCSServerBot)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/pschilly/filament-dcs-server-stats/fix-php-code-style-issues.yml?branch=main&style=for-the-badge)





This package is a series of widgets, pages and panels for Filament PHP and is loaded as a plugin. It interfaces with the DCS Server Bot API by Special-K to obtain statistics gathered from managed Digital Combat Simulator (DCS) servers.

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

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-dcs-server-stats-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-dcs-server-stats-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-dcs-server-stats-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentDcsServerStats = new Pschilly\FilamentDcsServerStats();
echo $filamentDcsServerStats->echoPhrase('Hello, Pschilly!');
```

## Testing

```bash
composer test
```

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
