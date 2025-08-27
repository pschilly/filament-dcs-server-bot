<?php

namespace Pschilly\FilamentDcsServerStats\Commands;

use Illuminate\Console\Command;

class FilamentDcsServerStatsInstallCommand extends Command
{
    public $signature = 'filament-dcs-server-stats:install';

    public $description = 'Install the Filament DCS Server Stats package';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
