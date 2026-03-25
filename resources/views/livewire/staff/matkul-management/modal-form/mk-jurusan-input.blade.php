<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.staff.matkul-management.modal-form.partial.kode-mk', ['kodeString' => 'jurusan_kode'])
            </div>

            <div class="sm:col-span-2">
                @include('livewire.staff.matkul-management.modal-form.partial.digit-semester')
            </div>

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
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
        'xResults' => $jurusanResults,
        'selectX' => 'selectJurusan',
        'modelString' => 'nama_jurusan',
        'resetXInput' => 'resetJurusanInput()',
        'typeXString' => 'jurusan',
        'nameXString' => 'Jurusan',
        'idString' => 'jurusan_id',
        'kodeString' => 'jurusan_kode',
        'searchString' => 'jurusan_search',
        'nameSearchString' => 'jurusanNameSearch',
        'fetchString' => 'fetchJurusan',
        'iconString' => 'book-open',
    ])

    @include('livewire.global.modal-form.search-input-array-form', [
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
        'wireLoadingParent' => 'selectJurusan, resetJurusanInput, selectJurusanForFilter, resetJurusanFilter',
    ])

    <div x-data x-init="$watch('$store.config.nama_matkul', value => console.log('nama_matkul: ', value))"></div>

    <div x-data x-init="$watch('$store.config.prodi_id_array', value => console.log('prodi_id_array: ', value))"></div>
    <div x-data x-init="$watch('$store.config.kode_pr', value => console.log('kode_pr: ', value))"></div>

    <div x-data x-init="$watch('$store.config.jurusan_kode', value => console.log('jurusan_kode: ', value))"></div>
    <div x-data x-init="$watch('$store.config.prodi_kode', value => console.log('prodi_kode: ', value))"></div>

    <div x-data x-init="$watch('$store.config.digit_semester', value => console.log('digit_semester: ', value))"></div>
    <div x-data x-init="$watch('$store.config.digit_mk', value => console.log('digit_mk: ', value))"></div>
    <div x-data x-init="$watch('$store.config.semester', value => console.log('semester: ', value))"></div>
    <div x-data x-init="$watch('$store.config.kode_blok', value => console.log('kode_blok: ', value))"></div>

    <div x-data x-init="$watch('$store.config.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
    <div x-data x-init="$watch('$store.config.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div>
</div>
