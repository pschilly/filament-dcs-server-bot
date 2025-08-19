<?php

declare(strict_types=1);

namespace Pschilly\FilamentDcsServerStats;

use Filament\Widgets\WidgetConfiguration;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use Pschilly\FilamentDcsServerStats\Widgets;
use Pschilly\FilamentDcsServerStats\Livewire as PluginLivewire;

class WidgetManager
{
    /**
     * @var array<string, string>
     */
    protected array $livewireComponents = [
        'serverselector' => PluginLivewire\ServerSelector::class,
    ];

    /**
     * @var array<int, class-string>
     */
    protected array $widgets = [
        Widgets\DailyPlayersChart::class,
        Widgets\TopPilots::class,
        Widgets\TopSquadrons::class,
        Widgets\ServerStatistics::class,
        Widgets\Highscore::class
    ];

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(): void
    {
        $this->enqueueWidgetsForRegistration();

        foreach ($this->livewireComponents as $componentName => $componentClass) {
            Livewire::component($componentName, $componentClass);
        }

        $this->livewireComponents = [];
    }

    protected function enqueueWidgetsForRegistration(): void
    {
        foreach ($this->widgets as $widget) {
            $this->queueLivewireComponentForRegistration($this->normalizeWidgetClass($widget));
        }
    }

    /**
     * @param  class-string | WidgetConfiguration  $widget
     * @return class-string
     */
    public function normalizeWidgetClass(string | WidgetConfiguration $widget): string
    {
        if ($widget instanceof WidgetConfiguration) {
            return $widget->widget;
        }

        return $widget;
    }

    protected function queueLivewireComponentForRegistration(string $component): void
    {
        $componentName = app(ComponentRegistry::class)->getName($component);

        $this->livewireComponents[$componentName] = $component;
    }
}
