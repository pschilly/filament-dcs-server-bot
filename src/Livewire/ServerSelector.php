<?php

namespace Pschilly\FilamentDcsServerStats\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ServerSelector extends Component
{
    public $servers = [];

    public $selectedServer = '';

    public function mount()
    {
        $response = Http::baseUrl('http://192.168.50.143:9876')->get('/servers')->json();

        $servers = ['' => 'All Servers']; // Default option for all servers
        $botServers = collect($response)
            ->pluck('name', 'name')
            ->toArray();
        $this->servers = array_merge($servers, $botServers);
    }

    public function updatedSelectedServer($value)
    {
        $this->dispatch('serverSelected', $value);
    }

    public function render()
    {
        return view('filament-dcs-server-stats::livewire.server-selector', [
            'servers' => $this->servers,
            'selectedServer' => $this->selectedServer,
        ]);
    }
}
