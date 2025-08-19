<?php

namespace Pschilly\DcsServerBotApi\Traits;

trait ServerSpecificResults
{
    public $serverName = null;

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
