<?php

namespace Pschilly\FilamentDcsServerStats\Traits;

trait ServerSpecificResults
{
    public $serverName = NULL;

    /**
     * Recieves the `emit` from the Livewire component ServerSelector and sets the public variable $serverName.
     */

    public function handleServerSelected($serverName)
    {
        if ($serverName == 0) {
            $this->serverName = NULL;
        } else {
            $this->serverName = $serverName;
        }
    }
}
