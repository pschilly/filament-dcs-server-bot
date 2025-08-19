<?php

namespace Pschilly\FilamentDcsServerStats;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Pschilly\FilamentDcsServerStats\Commands\FilamentDcsServerStatsCommand;
use Pschilly\FilamentDcsServerStats\Testing\TestsFilamentDcsServerStats;

class FilamentDcsServerStatsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-dcs-server-stats';

    public static string $viewNamespace = 'filament-dcs-server-stats';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands());

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // // Handle Stubs
        // if (app()->runningInConsole()) {
        //     foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
        //         $this->publishes([
        //             $file->getRealPath() => base_path("stubs/filament-dcs-server-stats/{$file->getFilename()}"),
        //         ], 'filament-dcs-server-stats-stubs');
        //     }
        // }

        // // Testing
        // Testable::mixin(new TestsFilamentDcsServerStats);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'pschilly/filament-dcs-server-stats';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-dcs-server-stats', __DIR__ . '/../resources/dist/components/filament-dcs-server-stats.js'),
            // Css::make('filament-dcs-server-stats-styles', __DIR__ . '/../resources/dist/filament-dcs-server-stats.css'),
            // Js::make('filament-dcs-server-stats-scripts', __DIR__ . '/../resources/dist/filament-dcs-server-stats.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [];
    }
}
