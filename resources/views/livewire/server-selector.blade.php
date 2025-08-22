{{-- filepath: resources/views/livewire/server-selector.blade.php --}}
<div>
    <x-filament::input.wrapper 
    prefix-icon="heroicon-m-server-stack">
        <x-filament::input.select wire:model.live="selectedServer">
            @foreach($servers as $name => $label)
                <option value="{{ $name }}">{{ $label }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
    
</div>
