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
                            isset($playerData['playtime']) ? round($playerData['playtime'] / 60) * 60 : 0
                        )->cascade()->totalHours
                    );
                @endphp
                <!-- Start Header -->
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight dark:text-white">{{ $nick }}'s Statistics</h2>
                        <div class="mt-4 flex flex-col sm:mt-4 sm:flex-row sm:flex-wrap sm:space-x-2">
                            <x-filament::badge icon="phosphor-clock-countdown-duotone" color="gray">
                                Flight Time - {{ $playTimeForHumans }}h
                            </x-filament::badge>
                            <x-filament::badge icon="plane_takeoff" color="success">
                                Takeoffs - {{ $playerData['takeoffs'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="plane_landing" color="primary">
                                Landings - {{ $playerData['landings'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="pilot_parachute" color="warning">
                                Ejections - {{ $playerData['ejections'] }}
                            </x-filament::badge>
                            <x-filament::badge icon="plane_crash" color="danger">
                                Crashes - {{ $playerData['crashes'] }}
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
                <div class="mt-6 fi-sc fi-sc-has-gap fi-grid lg:fi-grid-cols" style="--cols-lg: repeat(4, minmax(0, 1fr)); --cols-default: repeat(1, minmax(0, 1fr));">
                    @livewire(\Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\PveChart::class, ['playerData' => $playerData], key($playerData['id'] ?? $playerData['nick'] ?? Str::random()))
                    @livewire(\Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\PvpChart::class, ['playerData' => $playerData], key($playerData['id'] ?? $playerData['nick'] ?? Str::random()))
                    @livewire(\Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\ModuleChart::class, ['playerData' => $playerData], key($playerData['id'] ?? $playerData['nick'] ?? Str::random()))
                    @livewire(\Pschilly\FilamentDcsServerStats\Widgets\PlayerStats\SortieChart::class, ['playerData' => $playerData], key($playerData['id'] ?? $playerData['nick'] ?? Str::random()))
                </div>
                <!-- End Page Widgets -->

<pre>
    - lastSessionKills
    - lastSessionDeaths
    - credits
    - squadrons
{{  json_encode($playerData, JSON_PRETTY_PRINT) }}
</pre>
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