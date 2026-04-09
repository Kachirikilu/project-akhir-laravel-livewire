<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Referensi</h4>


    <div class="relative space-y-4">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">

            {{-- 1. REFERENSI UTAMA (CPMK) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-600 rounded-lg shadow-sm shadow-blue-200 dark:shadow-none">
                        <flux:icon.book-open variant="solid" class="size-4 text-white" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">Referensi CPMK</h3>
                        <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Sumber Utama Mata Kuliah</p>
                    </div>
                </div>

                {{-- WADAH DENGAN SCROLL --}}
                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin">
                    <template x-if="$store.rps.ref_cpmk.length === 0">
                        <div
                            class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                            <flux:icon.document-text class="size-8 text-zinc-300 mb-2" />
                            <p class="text-xs italic text-zinc-400">Belum ada referensi CPMK</p>
                        </div>
                    </template>

                    <template x-for="ref in $store.rps.ref_cpmk" :key="'main-' + ref.id">
                        <div x-data="{ expanded: false }"
                            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden transition-all hover:border-blue-300 dark:hover:border-blue-800 shadow-sm mb-3">

                            <div x-on:click="expanded = !expanded"
                                class="p-3 cursor-pointer flex items-center gap-3 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 border-l-4 border-l-blue-600">
                                <div class="size-7 flex-none flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-md font-bold text-xs"
                                    x-text="'ID' + ref.id"></div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200"
                                        x-text="ref.judul"></p>
                                    <p class="text-xs text-zinc-500 font-medium"
                                        x-text="ref.penulis + ' (' + (ref.tahun || '-') + ')'"></p>
                                </div>
                                <flux:icon.chevron-down variant="micro"
                                    class="text-zinc-400 transition-transform duration-200"
                                    x-bind:class="expanded ? 'rotate-180' : ''" />
                            </div>

                            <div x-show="expanded" x-collapse>
                                <div class="px-3 pb-3 pt-0 ml-1">
                                    <div
                                        class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-2">
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 italic leading-relaxed">
                                            <span class="font-bold text-zinc-500 not-italic">Sitasi:</span>
                                            <span
                                                x-text="ref.penulis + '. (' + ref.tahun + '). ' + ref.judul + '. ' + (ref.penerbit || '-')"></span>
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 pt-1">
                                            <span class="flex items-center gap-1.5 text-xs text-zinc-500">
                                                <flux:icon.building-library variant="micro" class="text-zinc-400" />
                                                <span x-text="ref.penerbit || 'Penerbit -'"></span>
                                            </span>
                                            <template x-if="ref.link">
                                                <a :href="ref.link" target="_blank"
                                                    class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:underline text-xs font-bold">
                                                    <flux:icon.link variant="micro" /> Buka Referensi
                                                </a>
                                            </template>
                                            <flux:badge color="blue" size="xs" x-text="ref.kode"></flux:badge>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 2. REFERENSI PENDUKUNG (Sub-CPMK) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-600 rounded-lg shadow-sm shadow-emerald-200 dark:shadow-none">
                        <flux:icon.document-magnifying-glass variant="solid" class="size-4 text-white" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">Referensi Sub-CPMK</h3>
                        <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Detail Sumber per Pertemuan</p>
                    </div>
                </div>

                {{-- WADAH DENGAN SCROLL --}}
                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin">
                    <template x-if="$store.rps.ref_scpmk.length === 0">
                        <div
                            class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                            <flux:icon.beaker class="size-8 text-zinc-300 mb-2" />
                            <p class="text-xs italic text-zinc-400">Belum ada referensi pendukung</p>
                        </div>
                    </template>

                    <template x-for="ref in $store.rps.ref_scpmk" :key="'sub-' + ref.id">
                        <div x-data="{ expanded: false }"
                            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden transition-all hover:border-emerald-300 dark:hover:border-emerald-800 shadow-sm mb-3">

                            <div x-on:click="expanded = !expanded"
                                class="p-3 cursor-pointer flex items-center gap-3 hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 border-l-4 border-l-emerald-500">
                                <div class="size-7 flex-none flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-md font-bold text-xs"
                                    x-text="'ID' + ref.id"></div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200"
                                        x-text="ref.judul"></p>
                                    <p class="text-xs text-zinc-500 font-medium"
                                        x-text="ref.penulis + ' • ' + (ref.tahun || '-')"></p>
                                </div>
                                <flux:icon.chevron-down variant="micro"
                                    class="text-zinc-400 transition-transform duration-200"
                                    x-bind:class="expanded ? 'rotate-180' : ''" />
                            </div>

                            <div x-show="expanded" x-collapse>
                                <div class="px-3 pb-3 pt-0 ml-1">
                                    <div
                                        class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-2">
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 italic leading-relaxed">
                                            <span class="font-bold text-zinc-500 not-italic">Sitasi:</span>
                                            <span
                                                x-text="ref.penulis + '. (' + ref.tahun + '). ' + ref.judul + '. ' + (ref.penerbit || '-')"></span>
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 pt-1">
                                            <span class="flex items-center gap-1.5 text-xs text-zinc-500">
                                                <flux:icon.building-library variant="micro" class="text-zinc-400" />
                                                <span x-text="ref.penerbit || 'Penerbit -'"></span>
                                            </span>
                                            <template x-if="ref.link">
                                                <a :href="ref.link" target="_blank"
                                                    class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 hover:underline text-xs font-bold">
                                                    <flux:icon.link variant="micro" /> Akses Materi
                                                </a>
                                            </template>
                                            <flux:badge color="emerald" size="xs" x-text="ref.kode"></flux:badge>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        @include('livewire.global.modal-form.search-input-array-form', [
            'alpine' => 'rps',
            'xResults' => $refResults,
            'selectX' => 'selectRefArray',
            'modelString' => 'nama_ref_search',
        
            'idString' => 'ref_id_array',
            'itemsAllString' => 'ref_items_array',
        
            'typeXString' => 'judul',
            'typeX2String' => 'penulis_tahun',
            'typeX3String' => 'penerbit',
        
            'nameXString' => 'Referensi',
            'nameX2String' => 'Tambah Referensi Baru',
            'searchString' => 'ref_search',
            'nameSearchString' => 'refNameSearch',
            'fetchString' => 'fetchRef',
            'iconString' => 'document-text',

            'parentIdString' => 'cpmk_id_array',
            'nameXParent' => 'CPMK',
            'wireLoading' => 'fetchRef',
        
            'isRequired' => 0,
        ])

    </div>

</div>
