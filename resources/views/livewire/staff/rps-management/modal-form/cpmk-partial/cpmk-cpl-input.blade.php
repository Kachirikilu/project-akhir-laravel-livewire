<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Capaian Pembelajaran Lulusan</h4>

    <div class="relative space-y-4">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        @include('livewire.global.modal-form.search-input-array-form', [
            'alpine' => 'cpmk',
            'xResults' => $cplResults['cpmk'] ?? [],
            'selectX' => 'selectRefArray',
            'modelString' => 'nama_cpl_search_cpmk',
            'key' => 'cpmk',
        
            'idString' => 'cpl_id_array.cpmk',
            'itemsAllString' => 'cpl_items_array.cpmk',
        
            'typeXString' => 'deskripsi',
        
            'nameXString' => 'Capaian Pembelajaran Lulusan',
            'nameSearchString' => 'cplNameSearch.cpmk',
            'fetchString' => 'fetchCPL',
            'iconString' => 'document-text',
            'wireLoading' => 'fetchCPL',
        ])

    </div>


</div>
