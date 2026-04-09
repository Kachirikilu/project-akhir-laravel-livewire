<div class="relative" wire:key="search-array-associative-{{ $typeXString }}-{{ $selectX }}" x-data="{
    open: false,
    search: @entangle($nameSearchString).live,
    items: @entangle($idString).live,
    itemsAll: @entangle($itemsAllString).live,
    subItems: @entangle($subItemsString).live,

    expanded: [],

    init() {
        if (!Array.isArray(this.items)) this.items = [];
        if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
    },

    init() {
        // 1. Pastikan inisialisasi array dasar
        if (!Array.isArray(this.items)) this.items = [];
        if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
        if (!Array.isArray(this.subItems)) this.subItems = [];

        this.$nextTick(() => {
            this.syncToRpsStore();
        });

        this.$watch('subItems', (value) => {
            this.syncToRpsStore();
        });
    },

    syncToRpsStore() {
        if (typeof $store.rps !== 'undefined') {
            $store.rps.update(this.subItems || []);
            $store.rps.setCountSCPMK(this.totalSubCPMK);
        }
    },

    parentSelectedId: @entangle($parentIdString ?? null).live,

    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    {{-- addItem(id, name, kode, subData) {
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);
            this.itemsAll.push(name);

            this.subItems.push(subData);
            this.syncToRpsStore();
        }
        this.search = '';
    }, --}}

    addItem(id, kode, name, name2, name3, subData) {
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);

            this.itemsAll.push({
                kode: kode,
                name: name,
                name2: name2,
                name3: name3
            });

            this.subItems.push(subData);
            this.syncToRpsStore();
        }
    },

    removeItem(index) {
        this.items.splice(index, 1);
        this.itemsAll.splice(index, 1);
        this.subItems.splice(index, 1);

        if (this.expanded === index) this.expanded = null;
        this.syncToRpsStore();
    },

    move(index, direction) {
        let to = index + direction;
        if (to < 0 || to >= this.items.length) return;
        const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];

        swap(this.items, index, to);
        swap(this.itemsAll, index, to);
        swap(this.subItems, index, to);

        if (this.expanded === index) this.expanded = to;
        else if (this.expanded === to) this.expanded = index;

        this.syncToRpsStore();
    },

    get grandTotalBobot() {
        return this.subItems.reduce((total, item) => {
            let subArray = item.scpmk || [];
            return total + subArray.reduce((subTotal, sub) => subTotal + Number(sub.bobot || 0), 0);
        }, 0);
    },

    get totalSubCPMK() {
        if (!this.subItems) return 0;
        return this.subItems.reduce((total, item) => {
            let subArray = item.scpmk || [];
            return total + subArray.length;
        }, 0);
    },

}">

    <label class="block text-sm font-semibold mb-2 text-[var(--contrast-main-text)]">
        {{ $nameXString }} 
        @if ($isRequired ?? true)
            <span class="text-red-500">*</span>
        @endif
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
                <div wire:key="res-{{ $typeXString }}-{{ $x['id'] }}"
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">
                    <div class="flex flex-col mr-4">

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
                                    itemsAll.splice(index, 1);
                                    subItems.splice(index, 1);
                                }
                            } else {
                                addItem(
                                    {{ $x['id'] }}, 
                                    '{{ $x['kode'] }}', 
                                    '{{ $x[$typeXString] }}', 
                                    @isset($typeX2String) '{{ $x[$typeX2String] ?? '' }}' @else null @endisset, 
                                    @isset($typeX3String) '{{ $x[$typeX3String] ?? '' }}' @else null @endisset,
                                    { 
                                        scpmk: {{ json_encode($x['scpmk']) }}, 
                                        ref: {{ json_encode($x['ref']) }},
                                        cpl: {{ json_encode($x['cpl']) }} 
                                    }
                                );
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
                <template x-if="grandTotalBobot <= 20">
                    <flux:badge color="red" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 80 && grandTotalBobot > 20">
                    <flux:badge color="orange" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 140 && grandTotalBobot > 80">
                    <flux:badge color="green" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot > 140">
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
        <div class="space-y-2 max-h-[625px] overflow-y-auto pr-1 scrollbar-medium">
            <template x-for="(id, index) in items" :key="id">
                <div
                    class="flex flex-col bg-[var(--second-table-color)] border border-[var(--border-table-color)] rounded-xl shadow-sm overflow-hidden transition-all mb-3 hover:border-[var(--focus-color)]">

                    {{-- Header Row (Mengikuti gaya desain baru) --}}
                    <div
                        class="flex items-start justify-between px-4 py-3 bg-white/40 dark:bg-black/10 transition-colors hover:bg-white/60 dark:hover:bg-black/20">

                        <div class="flex items-start gap-3 flex-1">
                            {{-- NOMOR URUT --}}
                            <span class="text-xs font-black text-[var(--hover-focus-color)] w-4 mt-0.5"
                                x-text="index + 1"></span>

                            <div class="flex flex-col gap-1 flex-1 cursor-pointer"
                                x-on:click="expanded.includes(index) ? expanded = expanded.filter(i => i !== index) : expanded.push(index)">

                                {{-- KODE SEBAGAI BADGE DI ATAS --}}
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <flux:icon icon="chevron-right" variant="mini"
                                            class="transition-transform duration-200"
                                            x-bind:class="expanded.includes(index) ? 'rotate-90 text-[var(--hover-focus-color)]' :
                                                'text-gray-400'" />
                                        <span
                                            class="text-xs font-bold px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white uppercase"
                                            x-text="itemsAll[index]?.kode"></span>
                                    </div>
                                    <div class="h-px flex-1 mb-1.5 bg-gray-200 dark:bg-neutral-800 opacity-40"></div>
                                </div>

                                {{-- NAMA UTAMA --}}
                                <span class="text-sm mb-1 font-semibold text-[var(--contrast-main-text)] leading-tight"
                                    x-text="itemsAll[index]?.name"></span>

                                {{-- DETAIL ID DAN TOTAL BOBOT DI BAWAH --}}
                                <div
                                    class="flex items-center flex-wrap text-xs text-[var(--contrast-second-text)] gap-y-1">
                                    <span class="font-bold text-[var(--hover-focus-color)]" x-text="'ID: ' + id"></span>
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span class="flex items-center gap-1">
                                        Total Bobot:
                                        <span class="font-black text-[var(--hover-focus-color)]"
                                            x-text="(subItems[index]?.scpmk || []).reduce((t, s) => t + Number(s.bobot || 0), 0) + '%'">
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex items-center gap-1 ml-4">
                            <div class="flex flex-col gap-0.5">
                                <button x-on:click="move(index, -1)" type="button" :disabled="index === 0"
                                    class="p-0.5 hover:bg-black/5 dark:hover:bg-white/10 rounded disabled:opacity-10">
                                    <flux:icon icon="chevron-up" variant="mini" class="size-4" />
                                </button>
                                <button x-on:click="move(index, 1)" type="button"
                                    :disabled="index === items.length - 1"
                                    class="p-0.5 hover:bg-black/5 dark:hover:bg-white/10 rounded disabled:opacity-10">
                                    <flux:icon icon="chevron-down" variant="mini" class="size-4" />
                                </button>
                            </div>
                            <button x-on:click="removeItem(index)" type="button"
                                class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500 rounded-md transition-colors ml-1">
                                <flux:icon icon="trash" variant="mini" class="size-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Expanded Sub-CPMK Table --}}
                    <div x-show="expanded.includes(index)" x-collapse>
                        <div class="px-4 pb-4 bg-white/20 dark:bg-black/5">
                            <div
                                class="border-t border-[var(--border-table-color)] pt-3 overflow-x-auto scrollbar-medium">
                                <table class="w-full text-xs text-left border-collapse min-w-[800px]">
                                    <thead>
                                        <tr
                                            class="text-gray-400 uppercase tracking-tighter border-b border-[var(--border-table-color)]">
                                            <th class="pb-3 px-4 text-center font-bold min-w-16">Kode</th>
                                            <th class="pb-3 px-4 min-w-32">Deskripsi</th>
                                            <th class="pb-3 px-4 min-w-32">Materi</th>
                                            <th class="pb-3 px-4 min-w-32">Metodologi</th>
                                            <th class="pb-3 px-4 min-w-32">Indikator</th>
                                            <th class="pb-3 px-4 text-center">Metode</th>
                                            <th class="pb-3 px-4 text-center">Bobot</th>
                                            <th class="pb-3 px-4 min-w-32">Deskripsi Tugas</th>
                                            <th class="pb-3 px-4 text-center">Waktu Tugas</th>
                                            <th class="pb-3 px-4 text-center">Waktu Mandiri</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[var(--border-table-color)]">
                                        <template x-for="sub in subItems[index]?.scpmk" :key="sub.id">
                                            <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                                                <td class="py-2.5 px-2">
                                                    <flux:badge color="fuchsia" size="sm"
                                                        class="py-0 px-1.5 text-xs font-bold uppercase">
                                                        <span x-text="sub.kode || '-'"></span>
                                                    </flux:badge>
                                                </td>
                                                <td class="py-2.5 px-2 leading-relaxed" x-text="sub.materi || '-'">
                                                </td>
                                                <td class="py-2.5 px-2 leading-relaxed"
                                                    x-text="sub.metodologi || '-'"></td>
                                                <td class="py-2.5 px-2 leading-relaxed" x-text="sub.indikator || '-'">
                                                </td>
                                                <td class="py-2.5 px-2 leading-relaxed" x-text="sub.deskripsi || '-'">
                                                </td>
                                                <td class="py-2.5 px-2 text-center leading-relaxed">
                                                    <div class="flex justify-center">
                                                        <template x-if="sub.metode === 'UTS' || sub.metode === 'UAS'">
                                                            <flux:badge color="amber" size="sm"
                                                                class="text-xs font-bold uppercase"
                                                                x-text="sub.metode"></flux:badge>
                                                        </template>
                                                        <template x-if="sub.metode === 'Teori'">
                                                            <flux:badge color="emerald" size="sm"
                                                                class="text-xs font-bold">Teori</flux:badge>
                                                        </template>
                                                        <template
                                                            x-if="['Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                                            <flux:badge color="cyan" size="sm"
                                                                class="text-xs font-bold" x-text="sub.metode">
                                                            </flux:badge>
                                                        </template>
                                                        <template
                                                            x-if="!['UTS', 'UAS', 'Teori', 'Praktik', 'Tugas', 'Hasil Projek'].includes(sub.metode)">
                                                            <flux:badge color="zinc" size="sm"
                                                                class="text-xs font-bold" x-text="sub.metode || '-'">
                                                            </flux:badge>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="py-2.5 px-2 text-center leading-relaxed font-black text-[var(--hover-focus-color)]"
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- totalSubCPMK --}}

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="academic-cap" variant="outline" class="mb-2 w-8 h-8" />
                <p class="text-xs font-medium italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>

        {{-- Footer Keseluruhan (Total Semua Sub-CPMK dari berbagai CPMK) --}}
        <template x-if="items.length > 0">
            <div
                class="mt-2 px-4 py-3 bg-[var(--focus-color)]/10 border border-[var(--focus-color)]/20 rounded-lg flex justify-between items-center">
                <span class="text-xs font-bold uppercase"
                    x-text="
                            grandTotalBobot <= 40 ? 'Bobot sangat kurang dari target:' : 
                            (grandTotalBobot <= 80 ? 'Bobot masih kurang dari target standar:' : 
                            (grandTotalBobot <= 100 ? 'Bobot ideal dan sudah memenuhi syarat:' : 
                            (grandTotalBobot <= 140 ? 'Bobot sudah mencukupi (Maksimal):' : 
                            'Bobot melebihi batas 140%, mohon tinjau kembali:')))
                    "></span>
                <template x-if="grandTotalBobot <= 40">
                    <flux:badge color="red" size="sm" variant="pill">
                        <span x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 80 && grandTotalBobot > 40">
                    <flux:badge color="orange" size="sm" variant="pill">
                        <span x-text="totalSubCPMK"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot <= 140 && grandTotalBobot > 80">
                    <flux:badge color="green" size="sm" variant="pill">
                        <span x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot > 140">
                    <flux:badge color="blue" size="sm" variant="pill">
                        <span x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
            </div>
        </template>

        <template x-if="items.length > 0">
            <div
                class="mt-2 px-4 py-3 bg-[var(--focus-color)]/10 border border-[var(--focus-color)]/20 rounded-lg flex justify-between items-center">
                <span class="text-xs font-bold uppercase"
                    x-text="
                            totalSubCPMK >= 14 ? 'Jumlah Sub-CPMK mencapai 14:' : 
                            'Jumlah Sub-CPMK masih kurang dari 14:'
                    "></span>
                <template x-if="totalSubCPMK < 14">
                    <flux:badge color="red" size="sm" variant="pill">
                        <span x-text="totalSubCPMK"></span>
                    </flux:badge>
                </template>
                <template x-if="totalSubCPMK >= 14">
                    <flux:badge color="green" size="sm" variant="pill">
                        <span x-text="totalSubCPMK"></span>
                    </flux:badge>
                </template>
            </div>
        </template>
    </div>


    @error($idString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
