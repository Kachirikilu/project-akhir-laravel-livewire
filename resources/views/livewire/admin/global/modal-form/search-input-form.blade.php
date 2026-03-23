<div class="relative" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    selectedId: @entangle($idString).live,
    selectedKode: @entangle($kodeString).live,
    isManual: false
}"
    x-effect="
        if ($store.config?.isEdit === 0) {
            search = '';
            selectedId = null;
            selectedKode = null;
        } else {
            selectedId2 = $store.config?.{{ $idString }};

            if (selectedId2 == '') {
                search = '';
                selectedId = null;
                selectedKode = null;
            } else {
                if('{{ $typeXString }}' == 'prodi') {
                    search = $store.config?.{{ $modelString }};
                } else {
                    search = '{{ $nameXString }} ' + $store.config?.{{ $modelString }};
                }
                selectedId = $store.config?.['{{ $idString }}'];
                selectedKode = $store.config?.['{{ $kodeString }}'];
            }
        }
"
    wire:key="search-input-form-{{ $typeXString }}">
    <label for="{{ $searchString }}" class="block text-sm font-medium">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.config?.colorIcon" />
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
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                placeholder-[var(--contrast-third-text)]
            w-full border rounded-lg pl-10 px-3 py-2 pr-10">

        {{-- Tombol Reset --}}
        @include('livewire.admin.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''; selectedId = null; selectedKode = null",
            'xWire' => $resetXInput,
            'xWire2' => $fetchString . '()',
            'xAlpine' => $idString,
            'xAlpine2' => $kodeString
            // 'xLivewire' => $resetXInput
            // 'xColor' => $colorIcon
        ])
    </div>

    {{-- Info Terpilih --}}
    <div x-show="selectedId && search" x-cloak>
        <p class="text-[var(--focus-color)] text-xs mt-1 font-medium italic">
            Terpilih: <span x-text="search" class="mx-1"></span> (Kode: <span x-text="selectedKode"></span>
            <span class="mx-1">|</span>
             ID: <span x-text="selectedId"></span>)
        </p>
    </div>

    {{-- DROPDOWN HASIL --}}
    <div x-show="open" x-cloak x-collapse.duration.300ms
        class="bg-[var(--pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">

        @forelse ($xResults as $x)
            <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
                @click="
    let newSearch = '{{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}';
    let newKode = '{{ filled($x['kode']) ? $x['kode'] : 'UNI' }}';

    search = newSearch;
    selectedId = {{ $x['id'] }};
    selectedKode = newKode;
    isManual = true;

    $store.config['{{ $idString }}'] = selectedId;
    $store.config['{{ $kodeString }}'] = selectedKode;
    $store.config.{{ $modelString }} = '{{ $x[$typeXString] }}';

    open = false;

    $wire.{{ $selectX }}({{ $x['id'] }}, '{{ $x[$typeXString] }}')
"
                class="px-4 py-2 cursor-pointer transition-colors duration-200
                bg-[var(--pop-up-color)] border-[var(--focus-color)]
                hover:bg-[var(--hover-pop-up-color)] hover:text-[var(--main-text)]
                {{-- border-b last:border-none  --}}
                text-sm">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <div class="text-[var(--contrast-main-text)] font-medium">
                            {{ (isset($noName) ? '' : $nameXString . ' ') . $x[$typeXString] }}
                        </div>

                        <div class="text-[var(--contrast-main-text)] text-xs flex items-center mt-0.5">
                            <span>- <span class="text-[var(--hover-focus-color)] font-medium">ID:
                                    {{ $x['id'] }}</span></span>

                            @if ($typeXString !== 'fakultas')
                                <span class="mx-1 text-[var(--contrast-second-text)]">|</span>
                                <span>Fakultas {{ $x['fakultas'] }}</span>
                            @endif
                        </div>

                    </div>
                    <span class="bg-[var(--focus-color)] text-[var(--main-text)] text-xs px-2 py-1 rounded-md ml-2">
                        {{ filled($x['kode']) ? $x['kode'] : 'UNI' }}
                    </span>
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
