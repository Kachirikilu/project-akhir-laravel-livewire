<div x-data="{
    open: false,
    localSearch: @entangle($xSearchQueryString),
    selectedName: @entangle($selectedXNameString)
}" class="relative w-full sm:flex-1">

    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-dynamic-component :component="'flux::icon.' . $iconString" variant="mini" class="text-gray-400 dark:text-gray-500" />
        </div>

        {{-- INPUT --}}
        <input type="text" x-model="localSearch" placeholder="{{ $placeholderString }}"
            @focus="
                open = true; 
                $event.target.select();
                $wire.{{ $inputXFilterString }}(); 
            "
            @input.debounce.300ms="
                open = true;
                $wire.set('{{ $xSearchQueryString }}', localSearch);
                $wire.{{ $inputXFilterString }}(); 
            "
            @click.outside="open = false" @keydown.escape.window="open = false"
            class="w-full h-10 pl-10 px-4 transition-all duration-200 rounded-lg shadow-sm border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
            autocomplete="off" />

        @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow' => 'localSearch.length > 0',
            'xClick' => "localSearch = ''",
            'xWire' => $resetXFilter,
        ])
    </div>


    {{-- DROPDOWN --}}
    <div x-show="open" x-cloak
        class="absolute z-[100] w-full mt-1 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-lg shadow-xl max-h-60 overflow-y-auto">
        @forelse ($xSearchResults as $x)
            <div wire:key="x-{{ $x['id'] }}"
                @click="
                    localSearch = '{{ ($typeOfXString == 'jurusan' ? 'Jurusan ' : ($typeOfXString == 'fakultas' ? 'Fakultas ' : '')) . $x[$typeOfXString] }}';
                    open = false;
                    $wire.{{ $selectXForFilterString }}({{ $x['id'] }});
                "
                class="px-4 py-2 cursor-pointer transition-colors duration-150 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-sm border-b border-gray-50 dark:border-neutral-700 last:border-none">

                <div class="font-medium text-gray-800 dark:text-gray-200">
                    {{ $typeOfXString == 'prodi' ? $x['prodi'] : ($typeOfXString == 'jurusan' ? 'Jurusan ' . $x['jurusan'] : 'Fakultas ' . $x['fakultas']) }}
                </div>
                
                <div class="text-[10px] text-gray-500 dark:text-gray-400 flex items-center mt-0.5">
                    <span>- <span class="text-blue-600 dark:text-blue-400 font-medium">ID: {{ $x['id'] }}</span></span>

                    @if ($typeOfXString !== 'fakultas')
                        <span class="mx-1 text-gray-300 dark:text-gray-600">|</span>
                        <span>Fakultas {{ $x['fakultas'] }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-4">
                <div wire:loading.remove target="{{ $inputXFilterString }}"
                    class="text-gray-400 dark:text-gray-500 italic text-xs text-center">
                    {{ $unfoundString }}
                </div>

                <div wire:loading flex target="{{ $inputXFilterString }}"
                    class="w-full flex-col items-center justify-center gap-2 text-indigo-500 dark:text-indigo-500">

                    <div class="flex justify-center">
                        <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <span class="block text-xs mt-1 text-center italic">
                        Menyaring data...
                    </span>
                </div>
            </div>
        @endforelse
    </div>
</div>