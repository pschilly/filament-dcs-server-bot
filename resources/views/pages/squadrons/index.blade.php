<x-filament-panels::page>   
    <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach ($squadrons as $squadron)
        <li class="col-span-1 flex flex-col divide-y divide-gray-200 rounded-lg bg-white text-center shadow-sm dark:divide-white/10 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
            <div class="flex flex-1 flex-col p-8">
            <img src="{{ $squadron['image_url'] }}" alt="" class="mx-auto size-48 shrink-0" />
            <h3 class="mt-6 text-xl font-medium text-gray-900 dark:text-white">{{ $squadron['name'] }}</h3>
            <dl class="mt-1 flex grow flex-col justify-between">
                <dt class="sr-only">Title</dt>
                <dd class="text-sm text-gray-500 dark:text-gray-400">{{ $squadron['description'] }}</dd>

            </dl>
            </div>
            <div>
            <div class="-mt-px flex divide-x divide-gray-200 dark:divide-white/10">
                <div class="flex w-0 flex-1">
                <div class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-4 text-sm font-semibold text-gray-900 dark:text-white">
                    <x-filament::modal slide-over>
                    <x-slot name="trigger">
                        <x-filament::button
                        size="sm"
                        color="info"
                        icon="pilot">
                            {{ count($squadron['members']) }} Pilots
                        </x-filament::button>
                    </x-slot>
                    <x-slot name="heading">
                        {{ $squadron['name'] }}
                    </x-slot>
                    <x-slot name="description">
                        Squadron Roster
                    </x-slot>

                    
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-white/5">             
                        @foreach ($squadron['members'] as $member)
                            <li class="flex justify-between gap-x-6 py-5">
                                <div class="flex min-w-0 gap-x-4">
                                {{-- <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="size-12 flex-none rounded-full bg-gray-50 dark:bg-gray-800 dark:outline dark:-outline-offset-1 dark:outline-white/10" /> --}}
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm/6 font-semibold text-gray-900 dark:text-white">{{ $member['nick'] }}</p>
                                    {{-- <p class="mt-1 truncate text-xs/5 text-gray-500 dark:text-gray-400">leslie.alexander@example.com</p> --}}
                                </div>
                                </div>
                                {{-- <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                <p class="text-sm/6 text-gray-900 dark:text-white">Co-Founder / CEO</p>
                                <p class="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">Last seen <time datetime="2023-01-23T13:23Z">3h ago</time></p>
                                </div> --}}
                            </li>
                        @endforeach
                    </ul>
                </x-filament::modal>
                </div>
                </div>
            </div>
            </div>
        </li>
        @endforeach
    </ul>
</x-filament-panels::page>
