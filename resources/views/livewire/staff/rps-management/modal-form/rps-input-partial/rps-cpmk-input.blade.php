<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Pilih Capaian Pembelajaran Mata Kuliah</h4>

    <div class="relative space-y-4">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])


        @include('livewire.global.modal-form.search-input-cpmk-form', [
            'alpine' => 'rps',
            'xResults' => $cpmkResults,
            'selectX' => 'selectCPMKArray',
            'modelString' => 'nama_cpmk_search',
        
            'idString' => 'cpmk_id_array',
            'itemsAllString' => 'cpmk_items_array',
            'subItemsString' => 'cpmk_sub_items_array',
        
            'typeXString' => 'deskripsi',
            'typeX2String' => 'total_bobot',
        
            'nameXString' => 'Capaian Pembelajaran Mata Kuliah (CPMK)',
            'searchString' => 'cpmk_search',
            'nameSearchString' => 'cpmkNameSearch',
            'fetchString' => 'fetchCPMK',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchCPMK',
        ])

    </div>


</div>
