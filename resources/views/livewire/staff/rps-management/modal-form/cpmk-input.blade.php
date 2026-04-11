<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Capaian Pembelajaran Lulusan</h4>


        <div class="grid sm:grid-cols-4 gap-3 items-end" x-data="{}"
            x-effect="$store.cpmk.kode_cpmk = ($store.cpmk.kode_cpmk_1 || '') + ($store.cpmk.kode_cpmk_2 || '')">


            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpmk',
                    'nameXString' => 'Kode CPMK',
                    'modelString' => 'kode_cpmk_1',
                    'iconString' => 'academic-cap',
                    'placeholder' => 'Masukkan huruf Kode CPMK',
                    'message' => $errors->first('kode_fk'),
                    'message' => $errors->first('kode_cpmk'),
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpmk',
                    'noLabel' => 1,
                    'modelString' => 'kode_cpmk_2',
                    'numberOnly' => 1,
                    'maxlength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 121104',
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'cpmk',
            'nameXString' => 'Deskripsi',
            'modelString' => 'deskripsi',
            'iconString' => 'academic-cap',
            'placeholder' => 'Masukkan deskripsi ringkas tentang CPMK...',
            'message' => $errors->first('deskripsi'),
        ])

        {{-- @include('livewire.global.modal-form.search-input-scpmk-form', [
            'alpine' => 'cpmk',
            'xResults' => $scpmkResults,
            'selectX' => 'selectSCPMKArray',
            'modelString' => 'nama_scpmk_search',
        
            'idString' => 'scpmk_id_array',
            'itemsAllString' => 'scpmk_items_array',
            'subItemsString' => 'scpmk_detail_items_array',
        
            'typeXString' => 'deskripsi',
            'typeX2String' => 'metode',
            'typeX3String' => 'bobot',
        
            'nameXString' => 'Sub-CPMK',
            'nameSearchString' => 'scpmkNameSearch',
            'fetchString' => 'fetchSCPMK',
            'iconString' => 'document-text',
        ]) --}}

        @include('livewire.global.modal-form.search-input-scpmk-form', [
            'alpine' => 'cpmk',
            'xResults' => $scpmkResults,
            'selectX' => 'selectSCPMKArray',
            'modelString' => 'nama_scpmk_search',
        
            'idString' => 'scpmk_id_array',
            'itemsAllString' => 'scpmk_items_array',
            'subItemsString' => 'scpmk_sub_items_array',
        
            'typeXString' => 'deskripsi',
            'typeX2String' => 'metode',
            'typeX3String' => 'bobot',
        
            'nameXString' => 'Sub-SCPMK',
            'nameSearchString' => 'scpmkNameSearch',
            'fetchString' => 'fetchSCPMK',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchSCPMK',
        ])

    </div>

    @include('livewire.staff.rps-management.modal-form.cpmk-input-partial.cpmk-referensi-input')

    <div x-data x-init="$watch('$store.cpmk.kode_cpmk', value => console.log('kode_cpmk: ', value))"></div>
    <div x-data x-init="$watch('$store.cpmk.kode_cpmk_1', value => console.log('kode_cpmk_1: ', value))"></div>
    <div x-data x-init="$watch('$store.cpmk.kode_cpmk_2', value => console.log('kode_cpmk_2: ', value))"></div>
    <div x-data x-init="$watch('$store.cpmk.deskripsi', value => console.log('deskripsi: ', value))"></div>
    {{-- <div x-data x-init="$watch('$store.cpmk.mk_items', value => console.log('mk_items: ', value))"></div> --}}

</div>
