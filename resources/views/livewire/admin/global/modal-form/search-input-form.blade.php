<div class="relative"
x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    selectedId: @entangle($idString).live,
    isManual: false
}"
{{-- x-init="
    $watch('$store.config.{{ $idString }}', value => {
            if ($store.config?.isEdit === 0) {
                search = '';
                selectedId = null;
            } else if (!isManual) {
                selectedId = value;

                if('{{ $typeXString }}' == 'prodi') {
                    search = $store.config?.{{ $modelString }};
                } else {
                    search = '{{ $nameXString }} ' + $store.config?.{{ $modelString }};
                }
            }
    })
" --}}
x-effect="
        if ($store.config?.isEdit === 0) {
            search = '';
            selectedId = null;
        } else {
            selectedId2 = $store.config?.{{ $idString }};
            {{-- search = $store.config?.{{ $modelString }}; --}}
            {{-- isManual = true; --}}

            if (selectedId2 == '') {
                search = '';
                selectedId = null;
            } else {
                if('{{$typeXString}}' == 'prodi') {
                    search = $store.config?.{{ $modelString }};
                } else {
                    search = '{{ $nameXString }} ' + $store.config?.{{ $modelString }};
                }
                selectedId = $store.config?.['{{ $idString }}'];
            }
        }
"
    wire:key="search-input-form-{{ $typeXString }}"
>
    <label for="{{ $searchString }}" class="block text-sm font-medium">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon"
 />
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @focus="
                open = true; 
                $event.target.select();
                $wire.{{ $fetchString }}(); 
            "
            @input.debounce.300ms="
                open = true;
                $wire.{{ $fetchString }}(search); 
            "
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $searchString }}"
            placeholder="Cari nama {{ $nameXString }}..."
            class="w-full border dark:border-neutral-700 rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow'   => 'search',
            'xClick' => "search = ''; selectedId = null",
            'xWire'   => $resetXInput,
            'xWire2'  => $fetchString . "()",
            'xAlpine' => $idString,
            // 'xLivewire' => $resetXInput
            // 'xColor' => $colorIcon
        ])
    </div>

    {{-- Info Terpilih --}}
    <div x-show="selectedId && search" x-cloak>
        <p class="text-xs text-indigo-600 dark:text-indigo-500 mt-1 font-medium italic">
            Terpilih: <span x-text="search"></span> (ID: <span x-text="selectedId"></span>)
        </p>
    </div>

    {{-- DROPDOWN HASIL --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">

        @forelse ($xResults as $x)
            <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
@click="
    let newSearch = '{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}';

    search = newSearch;
    selectedId = {{ $x['id'] }};
    isManual = true;

    $store.config['{{ $idString }}'] = selectedId;
    $store.config.{{ $modelString }} = '{{ $x[$typeXString] }}';

    open = false;

    $wire.{{ $selectX }}({{ $x['id'] }}, '{{ $x[$typeXString] }}')
"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition duration-150 border-b border-gray-50 dark:border-neutral-700 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span
                            class="font-semibold text-gray-800 dark:text-gray-200 leading-tight">{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}</span>

                        @if ($typeXString !== 'fakultas')
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Fakultas {{ $x['fakultas'] }}</span>
                        @endif

                        {{-- <span x-show="{{$typeXString}} !== 'fakultas'" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $nameXString }} {{ $x[$typeXString] }}</span> --}}
                    </div>
                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">ID:
                        {{ $x['id'] }}</span>
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">Data tidak ditemukan!</p>
            </div>
        @endforelse
    </div>
    @error($idString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
