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
            this.syncToCpmkStore();
        });

        this.$watch('subItems', (value) => {
            this.syncToCpmkStore();
        });
    },

    syncToCpmkStore() {
        if (typeof $store.cpmk !== 'undefined') {
            let allRefs = (this.subItems || []).flatMap(item => item.ref || []);
            
            $store.cpmk.ref_scpmk = Array.from(new Map(allRefs.map(r => [r.id, r])).values());
            
            $store.cpmk.setCountSCPMK(this.totalSubCPMK);
            $store.cpmk.total_bobot = this.grandTotalBobot;
        }
    },

    get grandTotalBobot() {
        return (this.subItems || []).reduce((total, item) => {
            // Karena subItems[index] berisi { ref: [...], scpmk: [...] }
            const subArray = item?.scpmk || [];
            return total + subArray.reduce((subTotal, sub) => {
                return subTotal + (parseFloat(sub?.bobot) || 0);
            }, 0);
        }, 0);
    },

    parentSelectedId: @entangle($parentIdString ?? null).live,

    get isParentReady() {
        return this.parentSelectedId != null && this.parentSelectedId != '';
    },

    addItem(id, kode, slot1, slot2, slot3, subData) {
        let normalizedId = Number(id);
        if (!this.items.map(i => Number(i)).includes(normalizedId)) {
            this.items.push(normalizedId);

            this.itemsAll.push({
                kode: kode,
                slot1: slot1,
                slot2: slot2,
                slot3: slot3
            });

            this.subItems.push(subData);
            this.syncToCpmkStore();
        }
    },

    removeItem(index) {
        this.items.splice(index, 1);
        this.itemsAll.splice(index, 1);
        this.subItems.splice(index, 1);

        if (this.expanded === index) this.expanded = null;
        this.syncToCpmkStore();
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

        this.syncToCpmkStore();
    },


    get totalSubCPMK() {
        return (this.subItems || []).reduce((total, item) => {
            return total + (item?.scpmk?.length || 0);
        }, 0);
    },

}">

    <label for="{{ $modelString }}"  class="block text-sm font-semibold mb-2 text-[var(--contrast-main-text)]">
        {{ $nameXString }}
        @if ($isRequired ?? true)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{-- 1. INPUT SEARCH --}}
    @include('livewire.global.modal-form.partial.input-search')

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
                            <span>Bobot: {{ $x['bobot'] }}%</span>
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
                                        // Kirim data lengkap untuk perhitungan
                                        ref: {{ json_encode($x['ref']) }},
                                        scpmk: [
                                            {
                                                id: {{ $x['id'] }},
                                                kode: '{{ $x['kode'] }}',
                                                deskripsi: '{{ $x['deskripsi'] }}',
                                                materi: '{{ $x['materi'] }}',
                                                metodologi: '{{ $x['metodologi'] }}',
                                                indikator: '{{ $x['indikator'] }}',
                                                metode: '{{ $x['metode'] }}',
                                                bobot: {{ $x['bobot'] }},
                                                w_tugas: '{{ $x['w_tugas'] }}',
                                                w_mandiri: '{{ $x['w_mandiri'] }}',
                                                tugas: '{{ $x['deskripsi'] }}' // Sesuaikan jika ada field tugas khusus
                                            }
                                        ]
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
                <template x-if="grandTotalBobot > 20 && grandTotalBobot < 80">
                    <flux:badge color="orange" size="sm" variant="pill">
                        Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                    </flux:badge>
                </template>
                <template x-if="grandTotalBobot >= 80 && grandTotalBobot <= 140">
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
                                    x-text="itemsAll[index]?.slot1"></span>

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
                    @include('livewire.global.modal-form.partial.scpmk-table')
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
