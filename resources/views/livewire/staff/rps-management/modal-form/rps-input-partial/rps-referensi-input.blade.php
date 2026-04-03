<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
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



</div>
