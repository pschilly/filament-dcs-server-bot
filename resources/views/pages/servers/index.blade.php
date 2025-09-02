<x-filament-panels::page>
    @php
        $serverStatusColor = [
            'Running' => 'success',
            'Paused' => 'warning',
            'Stopped' => 'danger'
        ];
        $serverStatusIcon = [
            'Running' => 'heroicon-s-play',
            'Paused' => 'heroicon-s-pause',
            'Stopped' => 'heroicon-s-stop'
        ];
    @endphp
    @foreach($servers as $server)
    <x-filament::section 
        collapsible
        collapsed
        persist-collapsed
        icon="heroicon-o-server"
        icon-color="{{ $serverStatusColor[$server['status']] ?? 'gray' }}"
        id="server-{{ $server['name'] }}"
    >
        <x-slot name="heading">
            {{ $server['name'] }}
        </x-slot>
        

        <x-slot name="afterHeader">
            @if($server['status'] != 'Shutdown')
            <x-filament::badge
            color="{{ ($server['password'] ? 'warning' : 'success') }}"
            icon="{{ $server['password'] ? 'heroicon-c-lock-closed' : 'heroicon-c-lock-open' }}">
                {{ ($server['password'] ? 'Private' : 'Public') }}
            </x-filament::badge>
            <x-filament::badge
            color="info"
            icon="heroicon-o-arrow-path-rounded-square">
            {{ \Carbon\Carbon::parse($server['restart_time'])->diffForHumans([ 'parts' => 2 ]) }}
            </x-filament::badge>
            @endif
            <x-filament::badge
            color="{{ $serverStatusColor[$server['status']] ?? 'gray' }}"
            icon="{{ $serverStatusIcon[$server['status']] ?? 'heroicon-o-question-mark-circle' }}">
                {{ $server['status'] }}
            </x-filament::badge>
        </x-slot>
        
        @if($server['status'] != 'Shutdown')
        <x-slot name="description">
            <div>
            <x-filament::badge
            color="gray"
            icon="heroicon-s-server"
            >
            {{ $server['address'] }}
            </x-filament::badge>
            <x-filament::badge
            color="info"
            icon="pilot">
                {{ $server['mission']['blue_slots_used'] . '/' . $server['mission']['blue_slots'] }}
            </x-filament::badge>
            <x-filament::badge
            color="danger"
            icon="pilot">
                {{ $server['mission']['red_slots_used'] . '/' . $server['mission']['red_slots'] }}
            </x-filament::badge>
        </div>
            <div class="mt-2">
                <x-filament::badge
                color="gray"
                icon="heroicon-o-document-text">
                    {{ $server['mission']['name'] }}
                </x-filament::badge>
                <x-filament::badge
                color="info"
                icon="heroicon-o-map">
                    {{ $server['mission']['theatre'] }}
                </x-filament::badge>
                <x-filament::badge
                color="gray"
                icon="heroicon-o-clock">
                    {{ \Carbon\Carbon::parse($server['mission']['date_time'])->format('d M y H:i') }}
                </x-filament::badge>
            </div>
        </x-slot>
        @endif
        {{-- Extensions --}}
            @foreach($server['extensions'] as $extension)
                @if($extension['name'] !== 'Required Mods')
                    <x-filament::badge
                        color="info"
                        icon="heroicon-o-cog">
                        {{ $extension['name'] }} - {{ $extension['version'] }}
                    </x-filament::badge>
                @endif
            @endforeach

            @foreach($server['extensions'] as $extension)
                @if($extension['name'] === 'Required Mods')
                <x-filament::fieldset class="mt-6">
                    <x-slot name="label">
                        Required Modifications
                    </x-slot>
                    
                    {!! nl2br($extension['value']) !!}
                </x-filament::fieldset>
                @endif
            @endforeach
            @if(!empty($server['players']))
            <x-filament::fieldset class="mt-6">
                <x-slot name="label">
                    Online Pilots
                </x-slot>
                
                @foreach($server['players'] as $player)
                    <x-filament::badge
                        color="{{ ($player['side'] == 'BLUE') ? 'info' : 'danger' }}"
                        icon="pilot">
                        {{ $player['nick'] }} - {{ $player['unit_type'] }}
                    </x-filament::badge>
                @endforeach
            </x-filament::fieldset>
            @endif

            @if($server['status'] == 'Shutdown')
            This server is currently offline.
            @endif

    </x-filament::section>
    @endforeach
</x-filament-panels::page>
