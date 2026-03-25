<div class="relative" x-data="{
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

    moveItem(index, direction) {
        let newIndex = index + direction;
        if (newIndex < 0 || newIndex >= this.items.length) return;

        let itemIds = [...this.items];
        let names = [...this.itemNames];
        let kodes = [...this.itemKodes];

        [itemIds[index], itemIds[newIndex]] = [itemIds[newIndex], itemIds[index]];
        [names[index], names[newIndex]] = [names[newIndex], names[index]];
        [kodes[index], kodes[newIndex]] = [kodes[newIndex], kodes[index]];

        this.items = itemIds;
        this.itemNames = names;
        this.itemKodes = kodes;
    }
}" wire:key="search-array-{{ $typeXString }}">

    <label class="block text-sm font-medium mb-2">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    {{-- 1. INPUT SEARCH --}}
    <div class="relative">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            @if ($wireLoading ?? null)
                <div wire:loading wire:target="{{ $wireLoading }}">
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
            <div @if ($wireLoading ?? null) wire:loading.remove wire:target="{{ $wireLoading }}" @endif>
                <flux:icon icon="{{ $iconString }}" variant="mini"
                    x-bind:class="isParentReady ? $store.config?.colorIcon : 'text-gray-400'" />
            </div>
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @if ($wireLoading ?? null) wire:loading.attr="disabled" wire:target="{{ $wireLoading }}" @endif
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
    <div x-show="open && isParentReady" x-cloak x-collapse @click.stop
        class="bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar relative">

        {{-- ANIMASI LOADING --}}
        <div @if ($wireLoading ?? null) wire:loading.attr="disabled" wire:target="{{ $wireLoading }}" @endif
            class="sticky top-0 left-0 right-0 bottom-0 z-[120] flex items-center justify-center bg-[var(--main-pop-up-color)]/80 backdrop-blur-[1px] py-10">
            <div class="flex flex-col items-center gap-2">
                <svg class="animate-spin h-6 w-6 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-[10px] font-medium text-[var(--focus-color)]">Mencari...</span>
            </div>
        </div>

        {{-- KONTEN LIST (Akan transparan saat loading) --}}
        <div
            @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" wire:loading.class="opacity-20 pointer-events-none" @endif">
            @forelse ($xResults as $x)
                <div
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                    <div class="flex flex-col">
                        <span
                            class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
                        <span class="text-[10px] text-gray-500 italic">ID: {{ $x['id'] }} | {{ $x['kode'] }} |
                            Fakultas {{ $x['fakultas'] }}</span>
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
                {{-- Jangan tampilkan "Tidak ditemukan" saat sedang loading agar tidak flicker --}}
                <div @if ($wireLoading ?? null) wire:loading.remove wire:target="{{ $wireLoading }}" @endif"
                    class="p-4 text-center italic text-sm opacity-50">
                    Data tidak ditemukan!
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

        <div class="space-y-2 relative"> {{-- Tambahkan relative --}}
            <template x-for="(id, index) in items" :key="id">
                <div {{-- Tambahkan class reorder-item --}}
                    class="reorder-item flex items-center justify-between bg-[var(--second-table-color)] border border-[var(--border-table-color)] px-3 py-2 rounded-lg shadow-sm"
                    {{-- Animasi Masuk & Keluar --}} x-transition:enter="transition-all duration-500 ease-out"
                    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition-all duration-300 ease-in"
                    x-transition:leave-end="opacity-0 scale-90">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-[var(--focus-color)] w-5" x-text="index + 1"></span>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-[var(--contrast-main-text)]"
                                x-text="itemNames[index]"></span>
                            <span class="text-[10px] opacity-70"
                                x-text="'ID: ' + id + ' | Kode: ' + itemKodes[index]"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        {{-- Tombol Atas --}}
                        <button @click="moveItem(index, -1)" type="button"
                            class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-20 transition-transform active:scale-90"
                            :disabled="index === 0">
                            <flux:icon icon="chevron-up" variant="mini" />
                        </button>

                        {{-- Tombol Bawah --}}
                        <button @click="moveItem(index, 1)" type="button"
                            class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded disabled:opacity-20 transition-transform active:scale-90"
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
        </div>
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
