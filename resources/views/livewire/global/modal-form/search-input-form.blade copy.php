<div class="relative" wire:key="search-input-form-{{ $typeXString }}-{{ $selectX }}" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    itemsAll: @entangle($itemsAllString).live,
    isManual: false
}"
x-effect="
    const config = $store.{{ $alpine ?? 'config' }};
    
    if (config?.isEdit === 0) {
        search = '';
        itemsAll = null;
    } else {
        let currentId = config?.['{{ $idString }}'];

        if (!currentId) {
            search = '';
            itemsAll = null;
        } else {
            search = config?.['{{ $modelString }}'];
            itemsAll = config?.['{{ $itemsAllString }}'];
        }
    }
">
    <label for="{{ $searchString }}" class="block text-sm font-medium">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @focus="
                open = true; 
                $event.target.select();
                $wire.{{ $fetchString }}(null, 'single'); 
            "
            @input.debounce.300ms="
                open = true;
                $wire.{{ $fetchString }}(search, 'single'); 
            "
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $searchString }}"
            placeholder="Cari nama {{ $nameXString }}..."
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                placeholder-[var(--contrast-third-text)]
            w-full border rounded-lg pl-10 px-3 py-2 pr-10">

        {{-- Tombol Reset --}}
        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''; itemsAll = null",
            'xWire' => $resetXInput,
            'xWire2' => $fetchString . "(null, 'single')",
            'xAlpine' => $itemsAllString,
        ])
    </div>

    {{-- Info Terpilih --}}
    <div x-show="itemsAll && search" x-cloak>
        <p class="text-[var(--focus-color)] text-xs mt-1 font-medium italic">
            Terpilih:
            <span x-text="itemsAll?.name" class="ml-1"></span>
            <span class="mx-1">|</span>
            Kode: <span x-text="itemsAll?.kode"></span>

            @if ($typeX2String ?? null)
                <span class="mx-1">|</span>
                <span x-text="itemsAll?.name2"></span>
            @endif
            @if ($typeX3String ?? null)
                <span class="mx-1">|</span>
                <span x-text="itemsAll?.name3"></span>
            @endif
            <span class="mx-1">|</span>
            ID: <span x-text="itemsAll?.id"></span>
        </p>
    </div>

    {{-- DROPDOWN HASIL --}}
    <div x-show="open" x-cloak {{-- x-collapse.duration.300ms --}} x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-80 overflow-y-auto custom-scrollbar">

        @forelse ($xResults as $x)
            <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
                @click="
                    let newSearch = '{{ $x[$typeXString] }}';
                    let newKode = '{{ filled($x['kode']) ? $x['kode'] : 'UNI' }}';

                    search = newSearch;
                    itemsAll = { 
                        id: {{ $x['id'] }},
                        kode: '{{ filled($x['kode']) ? $x['kode'] : 'UNI' }}',
                        name: '{{ $x[$typeXString] ?? '' }}',
                        name2: '{{ isset($typeX2String) ? ($x[$typeX2String] ?? '') : '' }}',
                        name3: '{{ isset($typeX3String) ? ($x[$typeX3String] ?? '') : '' }}'
                    };
                    isManual = true;

                    $store.{{ $alpine ?? 'config' }}['{{ $itemsAllString }}'] = itemsAll;
                    $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '{{ $x[$typeXString] }}';

                    open = false;

                    $wire.{{ $selectX }}({{ $x['id'] }}, '{{ $x[$typeXString] }}')
                "
                class="px-4 py-2 cursor-pointer transition-colors duration-200
                bg-[var(--main-pop-up-color)] border-[var(--focus-color)]
                hover:bg-[var(--hover-pop-up-color)] hover:text-[var(--main-text)]
                {{-- border-b last:border-none  --}}
                text-sm">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col mr-4">
                        <div class="text-[var(--contrast-main-text)] font-medium">
                            {{ $x[$typeXString] }}
                        </div>

                        <div class="text-[var(--contrast-main-text)] font-medium text-xs flex items-center mt-0.5">
                            <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                                    {{ $x['id'] }}</span></span>

                            @if ($typeX2String ?? null)
                                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                                <span>{{ $x[$typeX2String] }}</span>
                            @endif
                            @if ($typeX3String ?? null)
                                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                                <span>{{ $x[$typeX3String] }}</span>
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
                <div wire:loading @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                    <p class="text-sm text-[var(--focus-color)] font-medium animate-pulse">
                        Sedang mencari data {{ $nameXString ?? null }}...
                    </p>
                </div>

                <div wire:loading.remove @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                        Data {{ $nameXString ?? null }} tidak ditemukan!
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    @error($idString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
