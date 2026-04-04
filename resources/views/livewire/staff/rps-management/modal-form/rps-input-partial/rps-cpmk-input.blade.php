<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Pilih Capaian Pembelajaran Mata Kuliah</h4>

    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])


        @include('livewire.global.modal-form.search-input-associative-array-form', [
            'alpine' => 'rps',
            'xResults' => $cpmkResults,
            'selectX' => 'selectCPMK',
            'modelString' => 'nama_cpmk',
            // 'resetXInput' => 'resetCPMKInput()',
            'typeXString' => 'deskripsi',
            'nameXString' => 'CPMK',
            // 'noName' => 1,
            'idString' => 'cpmk_id_array',
            'kodeString' => 'cpmk_kode_array',
            'searchString' => 'cpmk_search',
            'nameSearchString' => 'cpmkNameSearch',
            'fetchString' => 'fetchCPMK',
            'iconString' => 'academic-cap',
        
            'selectedNameArray' => 'cpmk_name_array',
            'wireLoading' => 'fetchCPMK',
        ])

                    {{-- 3. GRADUATE LEARNING OUTCOMES (CPL) --}}
            <div class="space-y-6 mt-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-zinc-800 dark:bg-white rounded-lg shadow-sm">
                        <flux:icon.academic-cap variant="solid" class="size-4 text-white dark:text-zinc-900" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">Capaian Pembelajaran
                            Lulusan</h3>
                        <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Mapping CPL</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <template x-if="$store.rps.cpl_cpmk.length === 0">
                        <div
                            class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                            <flux:icon.clipboard class="size-8 text-zinc-300 mb-2" />
                            <p class="text-xs italic text-zinc-400">Belum ada mapping CPL</p>
                        </div>
                    </template>

                    <template x-for="cpl in $store.rps.cpl_cpmk" :key="cpl.id">
                        <div
                            class="group p-3 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl transition-all hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <div class="flex items-start gap-2">
                                <span
                                    class="flex-none text-[9px] font-black px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded"
                                    x-text="cpl.kode"></span>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed"
                                    x-text="cpl.deskripsi"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

    </div>


</div>
