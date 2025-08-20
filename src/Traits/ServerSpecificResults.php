<?php

namespace Pschilly\FilamentDcsServerStats\Traits;

trait ServerSpecificResults
{
    public $serverName = null;

    /**
     * Returns listeners for server selection.
     */
    public function getServerSpecificListeners(): array
    {
        return ['serverSelected' => 'handleServerSelected'];
    }

    /**
     * Recieves the `emit` from the Livewire component ServerSelector and sets the public variable $serverName.
     */
    public function handleServerSelected($serverName)
    {
        if ($serverName == 0) {
            $this->serverName = null;
        } else {
            $this->serverName = $serverName;
        }
    }
}
