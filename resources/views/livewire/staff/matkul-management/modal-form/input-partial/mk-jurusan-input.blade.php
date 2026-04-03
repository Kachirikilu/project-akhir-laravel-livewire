<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'labelString' => 'Kode Mata Kuliah',
                    'placeholder' => '---',
                    'iconString' => 'book-open'
                ])
            </div>

            <div class="sm:col-span-2">
                @include('livewire.staff.matkul-management.modal-form.partial.digit-semester')
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
                    'isRequired' => 1,
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
        'xResults' => $jurusanResults,
        'selectX' => 'selectJurusan',
        'modelString' => 'nama_jurusan_search',
        'resetXInput' => 'resetJurusanInput()',
        'typeXString' => 'jurusan',
        'nameXString' => 'Jurusan',
        'idString' => 'jurusan_id',
        'kodeString' => 'jurusan_kode',
        'searchString' => 'jurusan_search',
        'nameSearchString' => 'jurusanNameSearch',
        'fetchString' => 'fetchJurusan',
        'iconString' => 'book-open',
        'wireLoading' => 'fetchJurusan',
    ])

    @include('livewire.global.modal-form.search-input-array-form', [
        'alpine' => 'mk',
        'xResults' => $prodiResults,
        'selectX' => 'selectProdi',
        'modelString' => 'nama_prodi_array',
        // 'resetXInput' => 'resetProdiInput()',
        'typeXString' => 'prodi',
        'nameXString' => 'Program Studi',
        // 'noName' => 1,
        'idString' => 'prodi_id_array',
        'kodeString' => 'prodi_kode_array',
        'searchString' => 'prodi_search',
        'nameSearchString' => 'prodiNameSearch',
        'fetchString' => 'fetchProdi',
        'iconString' => 'academic-cap',
    
        'selectedNameArray' => 'prodi_name_array',
        'parentIdString' => 'jurusan_id',
        'nameXParent' => 'Jurusan',
        'wireLoading' => 'fetchProdi',
        'wireLoadingParent' => 'selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter',
    ])
</div>
