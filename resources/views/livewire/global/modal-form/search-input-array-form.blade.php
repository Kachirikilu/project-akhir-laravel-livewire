<div class="relative" wire:key="search-array-{{ $selectX }}" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    items: @entangle($idString).live,
    itemNames: @entangle($selectedNameArray).live,
    itemKodes: @entangle($kodeString).live,

    init() {
        if (!Array.isArray(this.items)) this.items = [];
        if (!Array.isArray(this.itemNames)) this.itemNames = [];
        if (!Array.isArray(this.itemKodes)) this.itemKodes = [];
    },

    parentSelectedId: @entangle($parentIdString ?? null).live,

    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    addItem(id, name, kode) {
        // Ubah id menjadi Number atau String secara konsisten
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);
            this.itemNames.push(name);
            this.itemKodes.push(kode);
        }
    },

    removeItem(index) {
        this.items.splice(index, 1);
        this.itemNames.splice(index, 1);
        this.itemKodes.splice(index, 1);
    },

    move(index, direction) {
        let to = index + direction;
        if (to < 0 || to >= this.items.length) return;
        const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];
        swap(this.items, index, to);
        swap(this.itemNames, index, to);
        swap(this.itemKodes, index, to);
    }
}" wire:key="search-array-{{ $typeXString }}">

    <label class="block text-sm font-medium mb-2">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    {{-- 1. INPUT SEARCH --}}
    <div class="relative">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            @if ($wireLoadingParent ?? null)
                <div wire:loading wire:target="{{ $wireLoadingParent }}">
                    <svg class="animate-spin h-4 w-4 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            @endif
            <div @if ($wireLoadingParent ?? null) wire:loading.remove wire:target="{{ $wireLoadingParent }}" @endif>
                <flux:icon icon="{{ $iconString }}" variant="mini"
                    x-bind:class="isParentReady ? $store.{{ $alpine ?? 'config' }}?.colorIcon : 'text-gray-400'" />
            </div>
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @if ($wireLoadingParent ?? null) wire:loading.attr="disabled" wire:target="{{ $wireLoadingParent }}" @endif
            :disabled="!isParentReady" @focus="open = true; $wire.{{ $fetchString }}(search);"
            @input.debounce.300ms="open = true; $wire.{{ $fetchString }}(search);" @click.outside="open = false"
            :placeholder="isParentReady ? 'Cari dan tambahkan {{ $nameXString }}...' :
                'Pilih {{ $nameXParent ?? 'Induk' }} terlebih dahulu...'"
            :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' :
                'bg-[var(--second-table-color)]'"
            class="border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 py-2 focus:ring-2 focus:ring-[var(--focus-color)] transition-all">

        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''",
        ])
    </div>

    {{-- 2. DROPDOWN HASIL --}}
    <div x-show="open && isParentReady" x-cloak
         {{-- x-collapse --}}
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-80 overflow-y-auto custom-scrollbar relative">

        {{-- KONTEN LIST (Akan transparan saat loading) --}}
        <div @if ($wireLoadingParent ?? null) wire:target="{{ $wireLoadingParent }}" wire:loading.class="opacity-20 pointer-events-none" @endif">
            @forelse ($xResults as $x)
                <div
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                    <div class="flex flex-col">
                        <span
                            class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
                                <div class="text-[var(--contrast-main-text)] text-xs flex items-center mt-0.5">
                            <span>- <span class="text-[var(--hover-focus-color)] font-medium">ID:
                                    {{ $x['id'] }}</span></span>
                            <span class="mx-1 text-[var(--contrast-second-text)]">|</span>
                            <span>{{ $x['kode'] }}</span>
                            @if ($typeXString == 'prodi' || $typeXString == 'jurusan')
                                <span class="mx-1 text-[var(--contrast-second-text)]">|</span>
                                <span>Fakultas {{ $x['fakultas'] }}</span>
                            @endif
                        </div>
                    </div>

                    <button type="button"
                        @click="
                        if (items.includes({{ $x['id'] }})) {
                            let index = items.indexOf({{ $x['id'] }});
                            if (index !== -1) {
                                items.splice(index, 1);
                                itemNames.splice(index, 1);
                                itemKodes.splice(index, 1);
                            }
                        } else {
                            addItem({{ $x['id'] }}, '{{ $x[$typeXString] }}', '{{ $x['kode'] }}');
                        }
                    "
                        :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white hover:bg-red-500' :
                            'bg-[var(--focus-color)] text-white'"
                        class="p-1.5 rounded-md transition-all group">

                        <template x-if="items.includes({{ $x['id'] }})">
                            <div class="relative">
                                <flux:icon icon="check" variant="mini" class="group-hover:hidden" />
                                <flux:icon icon="trash" variant="mini" class="hidden group-hover:block" />
                            </div>
                        </template>

                        <template x-if="!items.includes({{ $x['id'] }})">
                            <flux:icon icon="plus" variant="mini" />
                        </template>
                    </button>
                </div>
            @empty
                <div class="p-4 text-center">
                    <div wire:loading @if($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                        <p class="text-sm text-[var(--focus-color)] font-medium animate-pulse">
                            Sedang mencari data {{ $nameXString ?? null }}...
                        </p>
                    </div>

                    <div wire:loading.remove @if($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                            Data {{ $nameXString ?? null }} tidak ditemukan!
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. AREA OPSI TERPILIH (DI DALAM KOTAK) --}}
    <div
        class="mt-3 p-3 border-2 border-dashed border-[var(--border-table-color)] rounded-xl min-h-[100px] bg-gray-50/30 dark:bg-neutral-900/20">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Daftar Pilihan:</span>
            <span x-show="items.length > 0"
                class="text-[10px] px-2 py-0.5 bg-[var(--focus-color)] text-white rounded-full"
                x-text="items.length + ' Terpilih'"></span>
        </div>

        <div class="space-y-2">
            <template x-for="(id, index) in items" :key="id">
                <div
                    class="flex items-center justify-between bg-[var(--second-table-color)] border border-[var(--border-table-color)] px-3 py-2 rounded-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-[var(--focus-color)] w-5" x-text="index + 1"></span>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-[var(--contrast-main-text)]"
                                x-text="itemNames[index]"></span>
                            <span class="text-[10px] opacity-70" x-text="'ID: ' + id + ' | Kode: ' + itemKodes[index]">
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button @click="move(index, -1)" type="button"
                            class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-20"
                            :disabled="index === 0">
                            <flux:icon icon="chevron-up" variant="mini" />
                        </button>
                        <button @click="move(index, 1)" type="button"
                            class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-20"
                            :disabled="index === items.length - 1">
                            <flux:icon icon="chevron-down" variant="mini" />
                        </button>
                        <button @click="removeItem(index)" type="button"
                            class="p-1 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded ml-2">
                            <flux:icon icon="trash" variant="mini" />
                        </button>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="py-6 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="list-bullet" variant="outline" class="mb-1" />
                <p class="text-xs italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>