<?php

namespace Pschilly\FilamentDcsServerStats\Traits;

trait HasCleanAircraftNames
{
    protected array $aircraftNames = [
        'FA-18C_hornet' => 'F/A-18C',
        'F-16C_50' => 'F-16C',
        'A-10C_2' => 'A-10C II',
        // Add more mappings as needed
    ];

    public function getCleanAircraftName(string $module): string
    {
        return $this->aircraftNames[$module] ?? $module;
    }

    public function getAllAircraftNames(): array
    {
        return $this->aircraftNames;
    }
}