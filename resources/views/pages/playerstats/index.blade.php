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
                <div x-data x-init="document.title = '{{ e($this->getTitle()) }}'">
{{-- Desktop tabs --}}
<div class="hidden sm:block">
    <x-filament::tabs label="Statistics Sections" wire:model.live="activeTab">
        <x-filament::tabs.item
            :active="$activeTab === 'tab1'"
            wire:click="$set('activeTab', 'tab1')"
            icon="heroicon-m-bell"
        >
            Lifetime Statistics
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'tab2'"
            wire:click="$set('activeTab', 'tab2')"
            icon="heroicon-m-bell"
        >
            Session Statistics
            <x-slot name="badge">
                <x-filament::badge color="info">
                    New
                </x-filament::badge>
            </x-slot>
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'tab3'"
            wire:click="$set('activeTab', 'tab3')"
            icon="heroicon-m-bell"
        >
            Airframe Statistics
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'tab4'"
            wire:click="$set('activeTab', 'tab4')"
            icon="heroicon-m-bell"
        >
            Weapon Statistics
        </x-filament::tabs.item>
    </x-filament::tabs>
</div>

{{-- Mobile dropdown --}}
<div class="sm:hidden mb-4">
    <x-filament::input.wrapper>
    <x-filament::input.select 
        wire:model.live="activeTab"
        id="mobile_stat_cat">
        <option value="tab1">Lifetime Statistics</option>
        <option value="tab2">Session Statistics</option>
        <option value="tab3">Airframe Statistics</option>
        <option value="tab4">Weapon Statistics</option>
    </x-filament::input.select>
</x-filament::input.wrapper>
</div>

<div 
wire:show="activeTab === 'tab1'"

>

<h1>LIFETIME STATS</h1>
                        - PLAYTIME
                        - kills
                        - deaths
                        - pvp kills
                        - pvp deaths
                        - kdr
                        - kdr pvp
                        - takeoffs
                        - landings
                        - crashes
                        - ejects
                         - tk
</div>

<div wire:show="activeTab === 'tab2'" 
>

<h1>SESSION STATS</h1>
- kills
- deaths
</div>

<div wire:show="activeTab === 'tab3'"
>

<h1>VEHICLES</h1>
- module name & kills
</div>

<div wire:show="activeTab === 'tab4'"
>

 <h1>WEAPONS</h1>
                    top ones
</div>
                                    
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