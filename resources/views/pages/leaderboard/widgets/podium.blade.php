<div class="mx-auto">
  <div class="flex flex-col sm:flex-row justify-center items-center sm:items-end gap-4 p-6">
    <!-- Second Place (Silver) -->
    <div class="order-2 sm:order-1 flex flex-col items-center w-48 h-56 sm:w-52 sm:h-60 
                bg-gradient-to-br bg-gradient-to-br from-stone-400 via-gray-500 to-stone-200 
                shadow-xl rounded-2xl p-4 
                transform transition duration-300 hover:scale-105">
      <div class="text-5xl">🥈</div>
      <h2 class="mt-6 text-lg font-semibold text-center text-stone-900">{{ $second['nick'] }}</h2>
      <p class="text-stone-800 text-sm text-center">Viper Squadron</p>
      <div class="mt-auto text-lg font-bold text-stone-900">{{ $second['kills'] }} Kills!</div>
    </div>

    <!-- First Place (Gold) -->
    <div class="order-1 sm:order-2 flex flex-col items-center w-52 h-64 sm:w-60 sm:h-72 
                bg-gradient-to-br bg-gradient-to-br from-amber-200 via-amber-400 to-amber-100  
                shadow-2xl rounded-2xl p-6 sm:-mt-6
                transform transition duration-300 hover:scale-105">
      <div class="text-7xl">🥇</div>
      <h2 class="mt-6 text-xl font-bold text-center text-stone-900">{{ $first['nick'] }}</h2>
      <p class="text-stone-800 text-sm text-center">Eagle Squadron</p>
      <div class="mt-auto text-xl font-extrabold text-stone-950">{{ $first['kills'] }} Kills!</div>
    </div>

    <!-- Third Place (Bronze) -->
    <div class="order-3 sm:order-3 flex flex-col items-center w-44 h-52 sm:w-52 sm:h-52 
                bg-gradient-to-br bg-gradient-to-br from-amber-800 via-amber-600 to-amber-700 
                shadow-xl rounded-2xl p-4
                transform transition duration-300 hover:scale-105">
      <div class="text-5xl">🥉</div>
      <h2 class="mt-6 text-lg font-semibold text-center text-stone-900">{{ $third['nick'] }}</h2>
      <p class="text-stone-800 text-sm text-center">Falcon Squadron</p>
      <div class="mt-auto text-lg font-bold text-stone-900">{{ $third['kills'] }} Kills!</div>
    </div>
  </div>
</div>