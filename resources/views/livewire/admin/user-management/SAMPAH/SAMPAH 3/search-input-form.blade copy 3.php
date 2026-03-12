<div class="relative" x-data="{
    open_{{ $typeXString }}: false,
    search_{{ $typeXString }}: @entangle($nameSearchString),
    selectedId_{{ $typeXString }}: @entangle($idString).live,
}"
wire:key="container-select-{{ $typeXString }}-{{ time() }}"
x-init="console.log('Alpine Init for {{ $typeXString }}')"
>
    <label for="{{ $searchString }}" class="block text-sm font-medium text-gray-700">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.book-open variant="mini" class="{{ $colorIcon }}" />
        </div>

        <input x-model="search_{{ $typeXString }}" autocomplete="off" type="text"
            @focus="
        open_{{ $typeXString }} = true;
        $event.target.select();
        $wire.{{ $fetchString }}(search_{{ $typeXString }});
    "
            @input.debounce.300ms="
        open_{{ $typeXString }} = true;
        $wire.{{ $fetchString }}(search_{{ $typeXString }});
    "
            @click.outside="open_{{ $typeXString }} = false" @keydown.escape.window="open_{{ $typeXString }} = false"
            id="{{ $searchString }}" placeholder="Cari nama {{ $nameXString }}..."
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        {{-- <button x-show="search || selectedId" type="button" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-50"
            @click="search = ''; selectedId = null; $wire.{{ $resetXInput }}(); $wire.{{ $fetchString }}()"
            class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $colorIcon }} hover:text-red-500 transition duration-150">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button> --}}
        {{-- @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow'   => 'search || selectedId',
            'xClick' => "search = ''; selectedId = null",
            'xWire'   => $resetXInput,
            'xWire2'  => $fetchString . "()",
            'xColor' => $colorIcon
        ]) --}}
        @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow' => "search_$typeXString || selectedId_$typeXString",
            'xSearch' => "search_$typeXString",
            'xClick' => "search_$typeXString = ''; selectedId_$typeXString = null; open_$typeXString = false",
            'xWire' => $resetXInput,
            'xWire2' => $fetchString . '()',
            'xColor' => $colorIcon,
        ])
    </div>

    {{-- Info Terpilih --}}
    <div x-show="selectedId_{{ $typeXString }} && search_{{ $typeXString }}" x-cloak>
        <p class="text-xs text-indigo-600 mt-1 font-medium italic">
            Terpilih: <span x-text="search_{{ $typeXString }}"></span> (ID: <span
                x-text="selectedId_{{ $typeXString }}"></span>)
        </p>
    </div>

    {{-- DROPDOWN HASIL --}}
    <div x-show="open_{{ $typeXString }}" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto">

        @forelse ($xResults as $x)
            <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
                @click="
                    search_{{ $typeXString }} = '{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}'; 
                    selectedId_{{ $typeXString }} = {{ $x['id'] }}; 
                    open_{{ $typeXString }} = false; 
                    $wire.{{ $selectX }}({{ $x['id'] }}, '{{ $x[$typeXString] }}')
                "
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span
                            class="font-semibold text-gray-800 leading-tight">{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}</span>

                        @if ($typeXString !== 'fakultas')
                            <span class="text-xs text-gray-500 mt-0.5">Fakultas {{ $x['fakultas'] }}</span>
                        @endif

                        {{-- <span x-show="{{$typeXString}} !== 'fakultas'" class="text-xs text-gray-500 mt-0.5">{{ $nameXString }} {{ $x[$typeXString] }}</span> --}}
                    </div>
                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">ID:
                        {{ $x['id'] }}</span>
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
            </div>
        @endforelse
    </div>
</div>