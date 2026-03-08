<div x-data="{ open: false, selectedName: @entangle($selectedXNameString).live }">

    <div class="relative w-full sm:flex-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-dynamic-component 
                :component="'flux::icon.' . $iconString"
                variant="mini"
                class="text-gray-400"
            />
        </div>
        <input type="text" placeholder="{{ $placeholderString }}" x-model="selectedName"
            wire:model.live="{{ $xSearchQueryString }}" name="{{ $xSearchQueryString }}" @focus="open = true; $event.target.select()"
            @click.outside="open = false" @keydown.escape.window="open = false" @keydown.enter.prevent="open = false"
            class="w-full h-10 pl-10 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
            :class="{ 'pr-10': selectedName }" autocomplete="off" />


        @if ($selectedXId || $selectedXName)
            <button type="button" wire:click="{{ $resetXFilterString }}" $wire.xSearchQueryString = ''; open=false"
                class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
                title="Bersihkan Filter">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        @endif
    </div>

    @if (strlen($xSearchQuery) >= 0 && count($xSearchResults) > 0)
        <div x-show="open" x-cloak
            class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">

            @forelse ($xSearchResults as $x)
                <div wire:key="x-{{ $x['id'] }}"
                    wire:click="{{ $selectXForFilterString }}({{ $x['id'] }})" @click="open = false"
                    class="px-4 py-2 cursor-pointer hover:bg-indigo-50 text-gray-800 transition duration-150">
                    <div class="font-medium">
                        @if ($typeOfXString == 'prodi')
                            {{ $x['prodi'] }}
                        @elseif ($typeOfXString == 'jurusan')
                            {{ $x['jurusan'] }}
                        @elseif ($typeOfXString == 'fakultas')
                            Fakultas {{ $x['fakultas'] }}
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">
                        - ID: {{ $x['id'] }}
                        @if ($typeOfXString == 'prodi' || $typeOfXString == 'jurusan')
                            <span class="mx-1 text-gray-300">|</span> Fakultas {{ $x['fakultas'] }}
                        @endif
                    </div>
                </div>
              
            @empty
                <div class="px-4 py-2 text-gray-500 italic">{{ $unfoundString }}</div>
            @endforelse

        </div>
    @endif

</div>
