<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Pilih Dosen Pengajar</h4>


    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        @include('livewire.global.modal-form.search-input-dosen-form', [
            'alpine' => 'rps',
            'xResults' => $dosenResults,
            'selectX' => 'selectDosenArray',
            'modelString' => 'nama_dosen_search',
        
            'idString' => 'dosen_id_array',
            'itemsAllString' => 'dosen_items_array',
        
            'typeXString' => 'name',
            'typeX2String' => 'nidn_nidk',
            'typeX3String' => 'status',
        
            'nameXString' => 'Dosen Pengajar',
            'nameSearchString' => 'dosenNameSearch',
            'fetchString' => 'fetchDosen',
            'iconString' => 'user',
            'wireLoading' => 'fetchDosen',
        ])

    </div>

</div>
