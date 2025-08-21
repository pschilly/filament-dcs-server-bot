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
            <div class="bg-white shadow rounded p-6">
                @if(empty($playerData))
                    <div class="text-sm text-gray-600">No data available for <strong>{{ $nick }}</strong>.</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($playerData as $key => $value)
                            <div class="p-4 bg-gray-50 rounded">
                                <div class="text-lg font-semibold">
                                    @if(is_array($value) || is_object($value))
                                        <pre class="text-xs whitespace-pre-wrap">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    @script
    <script>
        const KEY = 'dcs_stats_player_nick';
        const stored = sessionStorage.getItem(KEY);

        $wire.dispatch('restorePlayerFromSession', { nick: stored });
    </script>
    @endscript

    @push('scripts')
    <script>
    document.addEventListener('livewire:navigated', function () {
        const KEY = 'dcs_stats_player_nick';
        const stored = sessionStorage.getItem(KEY);

        // Use the $wire proxy provided by Livewire v3
        if (stored && window.$wire) {
            window.$wire.restorePlayerFromSession(stored);
        }
    });
    </script>
    @endpush
</x-filament-panels::page>