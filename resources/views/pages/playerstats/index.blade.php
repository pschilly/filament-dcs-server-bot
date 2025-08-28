<x-filament-panels::page>
    <div class="space-y-6">
        {{-- form area (existing) --}}
        <div class="flex items-center gap-4">
            @if($showForm)
                <div class="w-full">
                    {{ $this->form }}
                </div>
            @endif
        </div>

        {{-- details / infolist section, shown after selection --}}
        @if(! $showForm)
            @if(empty($playerData))
                <div class="text-sm text-gray-600">No data available for <strong>{{ $nick }}</strong>.</div>
            @else
            <div x-data x-init="document.title = '{{ e($nick." - Player Statistics") }}'">
                @php
                    $playTimeForHumans = round(
                        Carbon\CarbonInterval::seconds(
                            isset($playerData['overall']['playtime']) ? round($playerData['overall']['playtime'] / 60) * 60 : 0
                        )->cascade()->totalHours
                    );
                @endphp
                <!-- Start Header -->
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight dark:text-white">
                         {{ $nick }}'s Statistics</h2>
                        <div class="mt-4 flex flex-col sm:mt-4 sm:flex-row sm:flex-wrap sm:space-x-2">
                            @if(is_null($playerData['current_server']))
                                <x-filament::badge icon="heroicon-o-server" color="gray">
                                    Offline
                                </x-filament::badge>
                                @else
                                <x-filament::badge icon="heroicon-s-server" color="success">
                                    Online - {{ $playerData['current_server'] }}
                                </x-filament::badge>
                            @endif
                            <x-filament::badge icon="phosphor-clock-countdown-duotone" color="gray">
                                Flight Time - {{ $playTimeForHumans }}h
                            </x-filament::badge>
                            <x-filament::badge icon="plane_takeoff" color="success">
                                Takeoffs - {{ $playerData['overall']['takeoffs'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="plane_landing" color="primary">
                                Landings - {{ $playerData['overall']['landings'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="pilot_parachute" color="warning">
                                Ejections - {{ $playerData['overall']['ejections'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="plane_crash" color="danger">
                                Crashes - {{ $playerData['overall']['crashes'] }}
                            </x-filament::badge>
                        </div>
                    </div>
                    <div class="mt-5 flex lg:mt-0 lg:ml-4">
                        <x-filament::button wire:click="clearSelection" color="primary" size="xl" icon="heroicon-o-chevron-double-left">
                            Select Another Player
                        </x-filament::button>
                    </div>
                </div>
                <!-- End Header -->
                <!-- Page Widgets -->
                @php
                    $widgetMap = [
                        'pve-chart'     => \Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\PveChart::class,
                        'pvp-chart'     => \Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\PvpChart::class,
                        'module-chart'  => \Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\ModuleChart::class,
                        'sortie-chart'  => \Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\SortieChart::class,
                        'combat-chart'  => \Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\CombatChart::class,
                    ];
                    $enabledWidgetsRaw = db_config('website.player-stats-widgets', []);
                    $enabledWidgets = collect($enabledWidgetsRaw)->pluck('widget-name')->all();
                @endphp

                <div class="mt-6 fi-sc fi-sc-has-gap fi-grid lg:fi-grid-cols" style="--cols-lg: repeat(4, minmax(0, 1fr)); --cols-default: repeat(1, minmax(0, 1fr));">
                    @foreach($enabledWidgets as $slug)
                        @if(isset($widgetMap[$slug]))
                            @livewire($widgetMap[$slug], ['playerData' => $playerData], key($playerData['id'] ?? $playerData['nick'] ?? Str::random()))
                        @endif
                    @endforeach
                </div>
                <!-- End Page Widgets -->
            </div>
            @endif
        @endif
    </div>

    @script
    <script>
        const KEY = 'dcs_stats_player_nick';
        const stored = sessionStorage.getItem(KEY);

        // Only emit if stored is a non-empty string
        if (stored) {
            $wire.dispatch('restorePlayerFromSession', { nick: stored });
        }

        $wire.on('dcs_stats:setSessionPlayer', (event) => {
            const nick = event;            
            if (nick) {
                sessionStorage.setItem(KEY, nick);
            }
        });
            
        $wire.on('dcs_stats:clearSessionPlayer', function () {
            sessionStorage.removeItem(KEY);
        });
    </script>
    @endscript
</x-filament-panels::page>