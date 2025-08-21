<?php

namespace Pschilly\FilamentDcsServerStats\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Pschilly\DcsServerBotApi\DcsServerBotApi;

class ServerSelector extends Component
{
    public $servers = [];
    public $selectedServer = '';

    public function mount()
    {
        $cacheKey = 'dcs_server_list';
        $response = Cache::remember($cacheKey, now()->addHours(6), function () {
            return DcsServerBotApi::getServerList();
        });

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
