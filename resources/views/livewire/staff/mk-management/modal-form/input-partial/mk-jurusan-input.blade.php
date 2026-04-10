<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'labelString' => 'Kode Mata Kuliah',
                    'kodeString' => 'jr_items',
                    'placeholder' => '---',
                    'iconString' => 'book-open'
                ])
            </div>

            <div class="sm:col-span-2">
                @include('livewire.staff.mk-management.modal-form.partial.digit-semester')
            </div>

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'mk',
                    'labelString' => 'Urutan Mata Kuliah',
                    'modelString' => 'digit_mk',
                    'numberOnly' => 1,
                    'maxlength' => 2,
                    'iconString' => 'identification',
                    'placeholder' => 'Contoh: 07',
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>
        @error('digit_mk')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('digit_mk') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'mk',
        'xResults' => $jrResults,
        'selectX' => 'selectJurusan',
        'modelString' => 'nama_jr_search',

        'idString' => 'jr_id',
        'itemsAllString' => 'jr_items',

        'resetXInput' => 'resetJurusanInput()',
        'typeXString' => 'jurusan',
        'typeX2String' => 'fakultas',
        
        'nameXString' => 'Jurusan',
        'searchString' => 'jurusan_search',
        'nameSearchString' => 'jrNameSearch',
        'fetchString' => 'fetchJurusan',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchJurusan'
    ])

    @include('livewire.global.modal-form.search-input-array-form', [
        'alpine' => 'mk',
        'xResults' => $prResults,
        'selectX' => 'selectProdiArray',
        'modelString' => 'nama_pr_search',

        'idString' => 'pr_id_array',
        'itemsAllString' => 'pr_items_array',

        'typeXString' => 'prodi',
        'typeX2String' => 'jurusan',
        'typeX3String' => 'fakultas',

        'nameXString' => 'Program Studi',
        'searchString' => 'prodi_search',
        'nameSearchString' => 'prNameSearch',
        'fetchString' => 'fetchProdi',
        'iconString' => 'academic-cap',
    
        'parentIdString' => 'jr_id',
        'nameXParent' => 'Jurusan',
        'wireLoading' => 'fetchProdi',
        'wireLoadingParent' => 'selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter',
    ])
</div>
