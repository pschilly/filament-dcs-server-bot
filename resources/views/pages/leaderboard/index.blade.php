@php
    $podium = $this->getPodiumData();

    $label = 'Kills';
    $key = 'kills';

    if (isset($podium['what'])) {
        switch ($podium['what']) {
            case 'kills':
                $label = 'Kills';
                $key = 'kills';
                break;
            case 'deaths':
                $label = 'Deaths';
                $key = 'deaths';
                break;
            case 'kdr':
                $label = 'KDR';
                $key = 'kdr';
                break;
            case 'credits':
                $label = 'Credits';
                $key = 'credits';
                break;
            case 'playtime':
                $label = 'Play Time';
                $key = 'playtime';
                break;
            // Add more cases if needed
            default:
                $label = ucfirst($podium['what']);
                $key = $podium['what'];
        }
    }

    // Format values: for playtime (seconds) show days and hours, otherwise round numeric values
    $formatValue = function ($value) use ($key) {
        if ($key === 'playtime') {
            $seconds = (int) ($value ?? 0);
            $hoursTotal = (int) round($seconds / 3600); // round to nearest hour
            if ($hoursTotal <= 0) {
                return '0h';
            }
            $days = intdiv($hoursTotal, 24);
            $hours = $hoursTotal % 24;
            if ($days > 0 && $hours > 0) {
                return "{$days}d {$hours}h";
            }
            if ($days > 0) {
                return "{$days}d";
            }
            return "{$hours}h";
        }

        if (is_numeric($value)) {
            return round($value, 2);
        }

        return $value ?? '';
    };

    $firstValue  = $formatValue($podium['first'][$key] ?? null);
    $secondValue = $formatValue($podium['second'][$key] ?? null);
    $thirdValue  = $formatValue($podium['third'][$key] ?? null);
@endphp


<x-filament-panels::page>
    <div class="mx-auto">
  <div class="flex flex-col sm:flex-row justify-center items-center sm:items-end gap-4 p-6">
    <!-- Second Place (Silver) -->
    <div class="order-2 sm:order-1 flex flex-col items-center w-48 h-56 sm:w-52 sm:h-60 
                bg-gradient-to-br bg-gradient-to-br from-stone-400 via-gray-500 to-stone-200 
                shadow-xl rounded-2xl p-4 
                transform transition duration-300 hover:scale-105">
      <div class="text-5xl">🥈</div>
      <h2 class="mt-6 text-lg font-semibold text-center text-stone-900">{{ $podium['second']['nick'] }}</h2>
      <div class="mt-auto text-lg font-bold text-stone-900 text-center">
        <span class="text-3xl">{{ $secondValue ?? 0 }}</span> <br /> {{ $label }}
      </div>
    </div>

    <!-- First Place (Gold) -->
    <div class="order-1 sm:order-2 flex flex-col items-center w-52 h-64 sm:w-60 sm:h-72 
                bg-gradient-to-br bg-gradient-to-br from-amber-200 via-amber-400 to-amber-100  
                shadow-2xl rounded-2xl p-6 sm:-mt-6
                transform transition duration-300 hover:scale-105">
      <div class="text-7xl">🥇</div>
      <h2 class="mt-6 text-xl font-bold text-center text-stone-900">{{ $podium['first']['nick'] }}</h2>
      <div class="mt-auto text-xl font-extrabold text-stone-950 text-center">
        <span class="text-3xl">{{ $firstValue ?? 0 }}</span> <br /> {{ $label }}
      </div>
    </div>

    <!-- Third Place (Bronze) -->
    <div class="order-3 sm:order-3 flex flex-col items-center w-48 h-56 sm:w-52 sm:h-60 
                bg-gradient-to-br bg-gradient-to-br from-amber-800 via-amber-600 to-amber-700 
                shadow-xl rounded-2xl p-4
                transform transition duration-300 hover:scale-105">
      <div class="text-5xl">🥉</div>
      <h2 class="mt-6 text-lg font-semibold text-center text-stone-900">{{ $podium['third']['nick'] }}</h2>
      <div class="mt-auto text-lg font-bold text-stone-900 text-center">
        <span class="text-3xl">{{ $thirdValue ?? 0 }}</span> <br /> {{ $label }}
      </div>
    </div>
  </div>
</div>

    {{  $this->table }}
</x-filament-panels::page>
