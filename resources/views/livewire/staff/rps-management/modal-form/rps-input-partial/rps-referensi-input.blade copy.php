<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Pilih Refenrensi Utama</h4>


    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])


        @include('livewire.global.modal-form.search-input-array-form', [
            'alpine' => 'rps',
            'xResults' => $refResults,
            'selectX' => 'selectRef',
            'modelString' => 'nama_ref',
            // 'resetXInput' => 'resetRefInput()',
            'typeXString' => 'judul',
            'nameXString' => 'Referensi',
            // 'noName' => 1,
            'idString' => 'ref_id_array',
            'kodeString' => 'ref_kode_array',
            'searchString' => 'ref_search',
            'nameSearchString' => 'refNameSearch',
            'fetchString' => 'fetchRef',
            'iconString' => 'document-text',
        
            'selectedNameArray' => 'ref_name_array',
            'wireLoading' => 'fetchRef',
        ])

    </div>

    {{-- Kasih pembatas untuk referensi dari CPMK

    list lengkap dengan bentuk yang bisa di extanded, saat extended menampilkan data lebih lengkap


    Pembatas juga untuk referensi dari SubCPMK
    
    
    Paling bawah CPL dari Sub-CPMK, kalau ada yang sama tampilkan salah satu saja --}}

    

<div x-data="{
    // Entangle langsung ke variabel lokal Alpine
    localItems: @entangle('items').live, 

    init() {
        // Sinkronisasi awal
        this.syncToStore(this.localItems);

        // Pantau perubahan dari Livewire
        this.$watch('localItems', (val) => {
            this.syncToStore(val);
        });
        
        Livewire.on('cpmk-updated', (data) => {
            console.log('Event received:', data);
            Alpine.store('rps').setItems(data[0]);
        });
    },

    syncToStore(data) {
        const validData = Array.isArray(data) ? data : [];
        console.log('Syncing to store...', validData);
        Alpine.store('rps').items = validData; // Langsung tembak properti store
        this.updateCounter(validData);
    },

    updateCounter(value) {
        let total = 0;
        value.forEach(cpmk => {
            // Gunakan optional chaining agar tidak error jika scpmk undefined
            total += (cpmk.scpmk?.length || 0);
        });
        Alpine.store('rps').setCountSCPMK(total);
    }
}">

    <pre class="text-[10px] bg-black text-white p-2 overflow-auto max-h-40" x-text="JSON.stringify($store.rps.items, null, 2)"></pre>


    <div class="bg-blue-500 text-white p-4">
    DEBUG LIVEWIRE: @json($items)
</div>

        <hr class="border-[var(--border-table-color)] opacity-50">

        {{-- 2. REFERENSI UTAMA (DARI CPMK) --}}
        <div x-data="{ open: true }">
            <button type="button" @click="open = !open" class="flex justify-between items-center w-full group">
                <div class="flex items-center gap-2">
                    <flux:icon icon="book-open" variant="mini" class="text-emerald-500" />
                    <h4 class="text-[var(--contrast-main-text)] text-sm font-bold uppercase tracking-wider">
                        Referensi Utama <span class="text-[10px] lowercase font-normal opacity-60">(dari CPMK)</span>
                    </h4>
                </div>
                <flux:icon icon="chevron-down" variant="mini" class="transition-transform duration-300"
                    ::class="open ? 'rotate-180' : ''" />
            </button>

            <div x-show="open" x-collapse class="mt-4 space-y-3">
                <template x-for="cpmk in ($store.rps.items || [])" :key="'cpmk-group-' + cpmk.id">
                    <div class="space-y-2">
                        <template x-for="ref in (cpmk.ref || [])" :key="'ref-main-' + ref.id">
                            <div
                                class="p-3 bg-emerald-50/40 border border-emerald-100 rounded-lg flex items-start gap-3 hover:bg-emerald-50 transition-colors">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-emerald-900" x-text="ref.judul"></p>
                                    <p class="text-[10px] text-emerald-700 mt-1">
                                        <span x-text="ref.penulis"></span> • <span x-text="ref.tahun"></span>
                                    </p>
                                </div>
                                <flux:badge size="xs" color="emerald" variant="outline" x-text="cpmk.kode">
                                </flux:badge>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- 3. REFERENSI PENDUKUNG (DARI SUB-CPMK) --}}
        <div x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex justify-between items-center w-full group">
                <div class="flex items-center gap-2">
                    <flux:icon icon="document-duplicate" variant="mini" class="text-amber-500" />
                    <h4 class="text-[var(--contrast-main-text)] text-sm font-bold uppercase tracking-wider">
                        Referensi Pendukung <span class="text-[10px] lowercase font-normal opacity-60">(khusus
                            Sub-CPMK)</span>
                    </h4>
                </div>
                <flux:icon icon="chevron-down" variant="mini" class="transition-transform duration-300"
                    ::class="open ? 'rotate-180' : ''" />
            </button>

            <div x-show="open" x-collapse class="mt-4 space-y-2">
                <template x-for="cpmk in ($store.rps.items || [])" :key="'sub-group-' + cpmk.id">
                    <div class="space-y-1">
                        <template x-for="sub in (cpmk.scpmk || [])" :key="'sub-item-' + sub.id">
                            <template x-for="sref in (sub.ref || [])" :key="'sref-' + sref.id">
                                <div
                                    class="p-2.5 bg-gray-50/50 border border-gray-200 rounded-md flex justify-between items-center group/item hover:border-amber-200">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700" x-text="sref.judul"></span>
                                        <span class="text-[9px] text-gray-500 italic"
                                            x-text="'Digunakan pada: ' + sub.kode"></span>
                                    </div>
                                    <flux:icon icon="link" variant="mini"
                                        class="text-gray-300 group-hover/item:text-amber-500" />
                                </div>
                            </template>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <hr class="border-[var(--border-table-color)] opacity-50">

        {{-- 4. CPL TERINTEGRASI (DARI SUB-CPMK) --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <flux:icon icon="academic-cap" variant="mini" class="text-blue-500" />
                <h4 class="text-[var(--contrast-main-text)] text-sm font-bold uppercase tracking-wider">
                    CPL yang Dibebankan
                </h4>
            </div>

            <div class="flex flex-wrap gap-2 p-3 bg-blue-50/30 border border-blue-100 rounded-xl">
                <template x-for="cpmk in ($store.rps.items || [])" :key="'cpl-cpmk-' + cpmk.id">
                    <template x-for="itemCpl in (cpmk.cpl || [])" :key="'cpl-item-' + itemCpl.id">
                        <flux:tooltip>
                            <x-slot name="content">
                                <div class="max-w-xs text-[10px]" x-text="itemCpl.deskripsi"></div>
                            </x-slot>

                            <div
                                class="px-3 py-1.5 bg-white border border-blue-200 text-blue-700 rounded-lg shadow-sm cursor-help hover:bg-blue-600 hover:text-white transition-all transform hover:-translate-y-0.5">
                                <span class="text-xs font-black" x-text="itemCpl.kode"></span>
                            </div>
                        </flux:tooltip>
                    </template>
                </template>

                {{-- Pesan jika kosong --}}
                <template x-if="!($store.rps.items || []).some(c => (c.cpl || []).length > 0)">
                    <span class="text-[10px] text-gray-400 italic">Belum ada CPL terdeteksi...</span>
                </template>
            </div>
        </div>

    </div>

</div>
