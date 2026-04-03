<div class="relative" wire:key="search-array-{{ $selectX }}" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    items: @entangle($idString).live,
    itemNames: @entangle($selectedNameArray).live,
    itemKodes: @entangle($kodeString).live,

    // Simpan data detail Sub-CPMK di Alpine
    subItems: [],
    expanded: [],

    init() {
        if (!Array.isArray(this.items)) this.items = [];
        if (!Array.isArray(this.itemNames)) this.itemNames = [];
        if (!Array.isArray(this.itemKodes)) this.itemKodes = [];
        // Inisialisasi subItems kosong sejumlah items yang sudah ada (saat edit mode)
        this.subItems = this.items.map(() => []);
    },

    parentSelectedId: @entangle($parentIdString ?? null).live,

    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    addItem(id, name, kode, subData) {
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);
            this.itemNames.push(name);
            this.itemKodes.push(kode);
            this.subItems.push(subData || []);
        }
        {{-- this.open = false; --}}
        this.search = '';
    },

    removeItem(index) {
        this.items.splice(index, 1);
        this.itemNames.splice(index, 1);
        this.itemKodes.splice(index, 1);
        this.subItems.splice(index, 1);
        if (this.expanded === index) this.expanded = null;
    },

    move(index, direction) {
        let to = index + direction;
        if (to < 0 || to >= this.items.length) return;
        const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];
        swap(this.items, index, to);
        swap(this.itemNames, index, to);
        swap(this.itemKodes, index, to);
        swap(this.subItems, index, to);
        if (this.expanded === index) this.expanded = to;
        else if (this.expanded === to) this.expanded = index;
    },

    get grandTotalBobot() {
        return this.subItems.reduce((total, subArray) => {
            return total + (Array.isArray(subArray) ? subArray.reduce((subTotal, sub) => subTotal + Number(sub.bobot || 0), 0) : 0);
        }, 0);
    },

    get totalSubCPMK() {
        return this.subItems.reduce((total, subArray) => {
            return total + (Array.isArray(subArray) ? subArray.length : 0);
        }, 0);
    }
}">

    <label class="block text-sm font-semibold mb-2 text-[var(--contrast-main-text)]">
        {{ $nameXString }} <span class="text-red-500">*</span>
    </label>

    {{-- 1. INPUT SEARCH --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="isParentReady ? $store.{{ $alpine ?? 'config' }}?.colorIcon : 'text-gray-400'" />
        </div>

        <input x-model="search" autocomplete="off" type="text" :disabled="!isParentReady"
            @focus="open = true; $wire.{{ $fetchString }}(search);"
            @input.debounce.300ms="open = true; $wire.{{ $fetchString }}(search);" @click.outside="open = false"
            :placeholder="isParentReady ? 'Cari dan tambahkan {{ $nameXString }}...' : 'Pilih Induk terlebih dahulu...'"
            :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' :
                'bg-[var(--second-table-color)]'"
            class="border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 py-2.5 focus:ring-2 focus:ring-[var(--focus-color)] transition-all">

        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''",
        ])
    </div>

    {{-- 2. DROPDOWN HASIL --}}
    <div x-show="open && isParentReady" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @click.stop
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-72 overflow-y-auto">

        <div>
            @forelse ($xResults as $x)
                <div
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                    <div class="flex flex-col">

                        <span
                        class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
                        <div class="text-[var(--contrast-main-text)] font-medium text-xs flex items-center mt-1">
                            <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                                    {{ $x['id'] }}</span></span>
                            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                            <span>{{ $x['kode'] }}</span>
                            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                            <span>Bobot: {{ $x['total_bobot'] }}%</span>
                        </div>
                    </div>

                    <button type="button"
                        x-on:click="
                            if (items.includes({{ $x['id'] }})) {
                                let index = items.indexOf({{ $x['id'] }});
                                if (index !== -1) {
                                    items.splice(index, 1);
                                    itemNames.splice(index, 1);
                                    itemKodes.splice(index, 1);
                                    subItems.splice(index, 1); // Penting: hapus detail sub-cpmk juga
                                }
                            } else {
                                addItem({{ $x['id'] }}, '{{ addslashes($x[$typeXString]) }}', '{{ $x['kode'] }}', {{ json_encode($x['sub_cpmk']) }});
                            }
                         "
                        x-bind:class="items.includes({{ $x['id'] }}) ? 'bg-green-500 hover:bg-red-500' :
                            'bg-[var(--focus-color)]'"
                        class="p-1.5 rounded-md text-white transition-all shadow-sm group">

                        <template x-if="items.includes({{ $x['id'] }})">
                            <div class="relative flex items-center justify-center">
                                <flux:icon icon="check" variant="mini" class="group-hover:hidden" />
                                <flux:icon icon="trash" variant="mini" class="hidden group-hover:block" />
                            </div>
                        </template>

                        {{-- State: Belum Terpilih (Tampilkan Plus) --}}
                        <template x-if="!items.includes({{ $x['id'] }})">
                            <div class="flex items-center justify-center">
                                <flux:icon icon="plus" variant="mini" />
                            </div>
                        </template>
                    </button>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. AREA OPSI TERPILIH --}}
    <div
        class="mt-4 p-4 border-2 border-dashed border-[var(--border-table-color)] rounded-xl bg-gray-50/30 dark:bg-neutral-900/10">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold uppercase tracking-widest text-gray-400">Daftar Terpilih:</span>
            <div class="flex items-center gap-2">
                <template x-if="grandTotalBobot <= 40">
                    <flux:badge color="red" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 80 && grandTotalBobot > 40">
                    <flux:badge color="orange" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 120 && grandTotalBobot > 80">
                    <flux:badge color="green" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot > 120">
                    <flux:badge color="blue" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <span x-show="items.length > 0"
                    class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full"
                    x-text="items.length + ' Terpilih'"></span>
            </div>
        </div>

        {{-- Daftar Item Berjejer ke Bawah (flex-col) --}}
        <div class="flex flex-col gap-3">
            <template x-for="(id, index) in items" :key="id">
                <div
                    class="flex flex-col bg-[var(--second-table-color)] border border-[var(--border-table-color)] rounded-xl shadow-sm overflow-hidden transition-all">

                    {{-- Header Row --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-white/40 dark:bg-black/10">
                        <div class="flex items-center gap-3 cursor-pointer flex-1"
                            x-on:click="expanded.includes(index) ? expanded = expanded.filter(i => i !== index) : expanded.push(index)">

                            <flux:icon icon="chevron-right" variant="mini" class="transition-transform duration-200"
                                x-bind:class="expanded.includes(index) ? 'rotate-90 text-[var(--hover-focus-color)]' : 'text-gray-400'" />

                            <div class="flex flex-col">
                                <span class="text-sm font-bold"
                                    x-text="itemNames[index]"></span>
                                <div class="text-xs mt-1">
                                    - <span class="font-bold text-[var(--hover-focus-color)]" x-text="'ID: ' + id "></span>
                                    <span class="mx-2">|</span>
                                    <span x-text="itemKodes[index]"></span>
                                    <span class="mx-2">|</span>
                                    <span>
                                        Bobot: <span x-text="subItems[index] ? subItems[index].reduce((t, s) => t + Number(s.bobot || 0), 0) : 0"></span>%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 ml-4">
                            <button x-on:click="move(index, -1)" type="button" :disabled="index === 0"
                                class="p-1 hover:bg-black/5 rounded disabled:opacity-20">
                                <flux:icon icon="chevron-up" variant="mini" />
                            </button>
                            <button x-on:click="move(index, 1)" type="button" :disabled="index === items.length - 1"
                                class="p-1 hover:bg-black/5 rounded disabled:opacity-20">
                                <flux:icon icon="chevron-down" variant="mini" />
                            </button>
                            <button x-on:click="removeItem(index)" type="button"
                                class="p-1 hover:bg-red-50 text-red-500 rounded">
                                <flux:icon icon="trash" variant="mini" />
                            </button>
                        </div>
                    </div>

                    {{-- Expanded Sub-CPMK Table dengan Scroll Horizontal --}}
                    <div x-show="expanded.includes(index)" x-collapse>
                        <div class="px-4 bg-white/20 dark:bg-black/5">
                            <div
                                class="border-t border-[var(--border-table-color)] pt-3 overflow-x-auto scrollbar-thin">
                                <table class="w-full text-[11px] text-left border-collapse min-w-[500px]">
                                    <thead>
                                        <tr
                                            class="text-gray-400 uppercase tracking-tighter border-b border-[var(--border-table-color)]">
                                            <th class="pb-3 px-4 text-center font-bold min-w-16">Kode</th>
                                            <th class="pb-3 px-4 min-w-32">Deskripsi</th>
                                            <th class="pb-3 px-4 min-w-24">Materi</th>
                                            <th class="pb-3 px-4 min-w-24">Metodologi</th>
                                            <th class="pb-3 px-4 min-w-24">Indikator</th>
                                            <th class="pb-3 px-4 text-center">Metode</th>
                                            <th class="pb-3 px-4 text-center">Bobot</th>
                                            <th class="pb-3 px-4 min-w-24">Deskripsi Tugas</th>
                                            <th class="pb-3 px-4 text-center">Waktu Tugas</th>
                                            <th class="pb-3 px-4 text-center">Waktu Mandiri</th>

                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[var(--border-table-color)]">
                                        <template x-for="sub in subItems[index]" :key="sub.id">
                                            <tr>
                                                <td class="py-2.5 px-2 font-bold">
                                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm" class="scale-90 transform origin-center py-0 px-1.5 text-[9px] uppercase font-bold">
                                                        <span x-text="sub.kode || '-'"></span>
                                                    </flux:badge>
                                                </td>

                                                <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.materi || '-'"></td>
                                                <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.metodologi || '-'"></td>
                                                <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.indikator || '-'"></td>
                                                 
                                                <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.deskripsi"></td>
                                                <td class="py-2.5  px-2 text-center italic opacity-90">
                                                    <div class="flex justify-center items-center">
                                                        {{-- 1. Kelompok Ujian (UTS/UAS) --}}
                                                        <template x-if="sub.metode === 'UTS' || sub.metode === 'UAS'">
                                                            <flux:badge icon="clipboard-document-check" color="amber" size="sm" class="scale-90 transform origin-center py-0 px-1.5 text-[9px] uppercase font-bold">
                                                                <span x-text="sub.metode"></span>
                                                            </flux:badge>
                                                        </template>

                                                        {{-- 2. Kelompok Teori / Materi --}}
                                                        <template x-if="sub.metode === 'Teori'">
                                                            <flux:badge icon="book-open" color="emerald" size="sm" class="scale-90 transform origin-center py-0 px-1.5 text-[9px] font-bold">
                                                                Teori
                                                            </flux:badge>
                                                        </template>

                                                        {{-- 3. Kelompok Praktik / Projek / Tugas --}}
                                                        <template x-if="['Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                                            <flux:badge icon="beaker" color="cyan" size="sm" class="scale-90 transform origin-center py-0 px-1.5 text-[9px] font-bold">
                                                                <span x-text="sub.metode"></span>
                                                            </flux:badge>
                                                        </template>

                                                        {{-- Default / Lainnya --}}
                                                        <template x-if="!['UTS', 'UAS', 'Teori', 'Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                                            <flux:badge icon="information-circle" color="zinc" size="sm" class="scale-90 transform origin-center py-0 px-1.5 text-[9px] font-bold">
                                                                <span x-text="sub.metode || '-'"></span>
                                                            </flux:badge>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="py-2.5 px-2 text-center font-black text-[var(--hover-focus-color)]"
                                                    x-text="sub.bobot + '%'"></td>
                                                <td class="py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.tugas || '-'"></td>
                                                <td class="py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.w_tugas || '-'"></td>     
                                                <td class="py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                                    x-text="sub.w_mandiri || '-'"></td>                            
                                                
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-t-2 border-double border-[var(--border-table-color)]">
                                            <td colspan="6"
                                                class="py-4 font-bold text-xs uppercase">Total Bobot
                                                CPMK Ini:</td>
                                            <td class="py-4 text-center font-black text-sm"
                                                x-text="subItems[index].reduce((t, s) => t + Number(s.bobot), 0) + '%'">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="items.length > 0">
                <div class="mt-2 flex flex-col gap-2">
                    <div class="px-4 py-3 bg-[var(--focus-color)]/10 border border-[var(--focus-color)]/20 rounded-lg flex justify-between items-center">
                        
                        <span class="text-sm font-bold uppercase"
                            x-text="
                                grandTotalBobot <= 40 ? '⚠️ Bobot sangat kurang dari target:' : 
                                (grandTotalBobot <= 80 ? '💡 Bobot masih kurang dari target:' : 
                                (grandTotalBobot <= 100 ? '✅ Bobot ideal dan memenuhi syarat:' : 
                                (grandTotalBobot <= 120 ? '✨ Bobot sudah mencukupi (Maksimal):' : 
                                '🚫 Bobot melebihi batas 120%:')))
                            ">
                        </span>
            
                        <template x-if="grandTotalBobot <= 40">
                            <flux:badge color="red" size="sm" variant="pill">
                                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                            </flux:badge>
                        </template>
                        <template x-if="grandTotalBobot <= 80 && grandTotalBobot > 40">
                            <flux:badge color="orange" size="sm" variant="pill">
                                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                            </flux:badge>
                        </template>
                        <template x-if="grandTotalBobot <= 120 && grandTotalBobot > 80">
                            <flux:badge color="green" size="sm" variant="pill">
                                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                            </flux:badge>
                        </template>
                        <template x-if="grandTotalBobot > 120">
                            <flux:badge color="blue" size="sm" variant="pill">
                                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                            </flux:badge>
                        </template>
                    </div>


                    {{-- <div class="px-4 py-3 border rounded-lg flex justify-between items-center transition-all">
            <div class="flex flex-col">
                <span class="text-sm font-bold uppercase">Total Frekuensi Sub-CPMK:</span>
                <span class="text-[10px] italic opacity-80" 
                    x-text="totalSubCPMK >= 14 ? 'Syarat 14 pertemuan terpenuhi.' : 'Minimal dibutuhkan 14 Sub-CPMK (Pertemuan).'"></span>
            </div>

            <flux:badge 
                :color="totalSubCPMK >= 14 ? 'green' : 'red'" 
                size="sm" variant="pill">
                <span x-text="totalSubCPMK"></span> / 14
            </flux:badge>
        </div>
                </div>
            </template> --}}

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="py-12 flex flex-col items-center justify-center opacity-30">
                <flux:icon icon="academic-cap" variant="outline" class="mb-2 w-8 h-8" />
                <p class="text-xs font-medium italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>
    </div>

    @error($modelString)
        <span class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1">
            <flux:icon icon="exclamation-circle" variant="mini" /> {{ $message }}
        </span>
    @enderror
</div>
