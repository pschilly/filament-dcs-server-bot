<?php

namespace Pschilly\FilamentDcsServerStats\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pschilly\FilamentDcsServerStats\FilamentDcsServerStats
 */
class SkelFilamentDcsServerStatseton extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Pschilly\FilamentDcsServerStats\FilamentDcsServerStats::class;
    }
}
