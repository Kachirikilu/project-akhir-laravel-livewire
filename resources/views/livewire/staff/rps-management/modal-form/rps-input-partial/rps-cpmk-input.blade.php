<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Pilih Capaian Pembelajaran Semester</h4>

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

    </div>


</div>
