<div class="relative" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    items: @entangle($idString).live,
    itemNames: @entangle('selectedProdiNameArray').live,
    itemKodes: @entangle($kodeString).live,
    
    // 🔹 Hubungkan langsung ke variabel jurusan_id di Livewire
    parentSelectedId: @entangle('jurusan_id').live,

    // 🔹 Sekarang getter ini akan reaktif karena memantau parentSelectedId
    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    addItem(id, name, kode) {
        if (!this.items.includes(id)) {
            this.items.push(id);
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
            {{-- Spinner muncul saat pilih jurusan atau reset --}}
            <div wire:loading wire:target="selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter">
                <svg class="animate-spin h-4 w-4 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div wire:loading.remove wire:target="selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter">
                <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="isParentReady ? $store.config?.colorIcon : 'text-gray-400'" />
            </div>
        </div>

        <input x-model="search" autocomplete="off" type="text" 
            wire:loading.attr="disabled"
            wire:target="selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter" 
            :disabled="!isParentReady"
            @focus="open = true; $wire.{{ $fetchString }}(search);"
            @input.debounce.300ms="open = true; $wire.{{ $fetchString }}(search);" 
            @click.outside="open = false"
            :placeholder="isParentReady ? 'Cari dan tambahkan {{ $nameXString }}...' : 'Pilih Jurusan terlebih dahulu...'"
            :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' : 'bg-[var(--second-table-color)]'"
            class="border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 py-2 focus:ring-2 focus:ring-[var(--focus-color)] transition-all">
    </div>

    {{-- 2. DROPDOWN HASIL (Ganti parentSelected menjadi isParentReady) --}}
    <div x-show="open && isParentReady" x-cloak x-collapse
        class="bg-[var(--pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">
        @forelse ($xResults as $x)
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
                    <span class="text-[10px] text-gray-500 italic">ID: {{ $x['id'] }} | {{ $x['fakultas'] }}</span>
                </div>

                <button type="button" @click="addItem({{ $x['id'] }}, '{{ $x[$typeXString] }}', '{{ $x['kode'] }}')"
                    :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white' : 'bg-[var(--focus-color)] text-white'"
                    class="p-1.5 rounded-md transition-all">
                    <template x-if="items.includes({{ $x['id'] }})"> <flux:icon icon="check" variant="mini" /> </template>
                    <template x-if="!items.includes({{ $x['id'] }})"> <flux:icon icon="plus" variant="mini" /> </template>
                </button>
            </div>
        @empty
            <div class="p-4 text-center italic text-sm opacity-50">Data tidak ditemukan.</div>
        @endforelse
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
                            <span class="text-[10px] opacity-70" x-text="'Kode: ' + itemKodes[index]"></span>
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
                <p class="text-xs italic">Belum ada prodi yang dipilih</p>
            </div>
        </div>
    </div>
</div>
