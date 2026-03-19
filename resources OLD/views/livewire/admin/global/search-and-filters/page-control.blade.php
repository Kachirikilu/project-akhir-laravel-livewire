<div class="flex items-center justify-end {{ ($withFull ?? true) ? 'pb-4 ml-4' : '' }}">

    {{-- <label class="text-sm font-medium text-gray-500 mr-2 whitespace-nowrap">Tampilkan:</label> --}}
    <div x-data="{ open: false, selected: @entangle('perPage').live }" class="relative w-15 **z-20**" @click.away="open = false">
        <button type="button" @click="open = !open"
            class="cursor-pointer flex items-center justify-between border border-gray-300 rounded-md shadow-sm 
                       py-1 px-2 text-sm w-full bg-white transition duration-150 hover:border-indigo-500">
            <span x-text="selected">8</span>
            <svg class="h-4 w-4 ml-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-cloak
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-20 mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-gray-300 ring-opacity-5 focus:outline-none overflow-hidden"
            role="menu" aria-orientation="vertical" tabindex="-1">
            @foreach ($perPageOptions as $option)
                <li wire:key="perPage-{{ $option }}" @click="selected = {{ $option }}; open = false"
                    class="text-gray-700 block px-3 py-1 text-sm cursor-pointer hover:bg-indigo-500 hover:text-white"
                    :class="{ 'bg-indigo-100 font-semibold text-indigo-700': selected == {{ $option }} }">
                    {{ $option }}
                </li>
            @endforeach
        </ul>
    </div>

    @if ($withFull ?? true)
        <span class="text-sm font-medium text-gray-500 ml-2">Baris</span>
    @endif

</div>
