<div class="flex items-center justify-end {{ ($withFull ?? true) ? 'pb-4 ml-4' : '' }}">

    <div x-data="{ open: false, selected: @entangle('perPage').live }" class="relative w-15 z-20" @click.away="open = false">
        {{-- Tombol Utama --}}
        <button type="button" @click="open = !open"
            class="cursor-pointer flex items-center justify-between border border-gray-300 dark:border-neutral-600 rounded-md shadow-sm 
                   py-1 px-2 text-sm w-full bg-white dark:bg-neutral-700 text-gray-700 dark:text-gray-200 
                   transition duration-150 hover:border-indigo-500 dark:hover:border-indigo-400">
            <span x-text="selected">8</span>
            <svg class="h-4 w-4 ml-1 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-cloak
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-20 mt-1 w-full rounded-md bg-white dark:bg-neutral-700 shadow-lg ring-1 ring-gray-300 dark:ring-gray-600 ring-opacity-5 focus:outline-none overflow-hidden"
            role="menu" aria-orientation="vertical" tabindex="-1">
            
            @foreach ($perPageOptions as $option)
                <li wire:key="perPage-{{ $option }}" @click="selected = {{ $option }}; open = false"
                    class="block px-3 py-1 text-sm cursor-pointer transition-colors duration-150
                           text-gray-700 dark:text-gray-200 hover:bg-indigo-500 dark:hover:bg-indigo-600 hover:text-white"
                    :class="{ 
                        'bg-indigo-100 dark:bg-indigo-900/50 font-semibold text-indigo-700 dark:text-indigo-300': selected == {{ $option }} 
                    }">
                    {{ $option }}
                </li>
            @endforeach
        </ul>
    </div>

    @if ($withFull ?? true)
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-2">Baris</span>
    @endif

</div>