<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.admin.matkul-management.modal-form.partial.kode-mk')
            </div>

            <div class="sm:col-span-2">
                @include('livewire.admin.matkul-management.modal-form.partial.digit-semester')
            </div>

            <div class="sm:col-span-2">
                @include('livewire.admin.global.modal-form.input-form', [
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

    @include('livewire.admin.global.modal-form.search-input-array-form', [
        'xResults' => $prodi_results,
        'selectX' => 'selectProdi',
        'modelString' => 'nama_prodi',
        // 'resetXInput' => 'resetProdiInput()',
        'typeXString' => 'prodi',
        'nameXString' => 'Program Studi',
        // 'noName' => 1,
        'idString' => 'prodi_id_array',
        'kodeString' => 'selected_kode_pr_array',
        'searchString' => 'prodi_search',
        'nameSearchString' => 'prodi_name_search',
        'fetchString' => 'fetchProdi',
        'iconString' => 'academic-cap',
    
        'selectedNameArray' => 'selectedProdiNameArray',
    ])


    <div x-data x-init="$watch('$store.config.nama_matkul', value => console.log('nama_matkul: ', value))"></div>

    <div x-data x-init="$watch('$store.config.prodi_id', value => console.log('prodi_id: ', value))"></div>
    <div x-data x-init="$watch('$store.config.kode_pr', value => console.log('kode_pr: ', value))"></div>

    <div x-data x-init="$watch('$store.config.selected_kode_pr', value => console.log('selected_kode_pr: ', value))"></div>
    <div x-data x-init="$watch('$store.config.selected_kode_fk', value => console.log('selected_kode_fk: ', value))"></div>

    <div x-data x-init="$watch('$store.config.digit_semester', value => console.log('digit_semester: ', value))"></div>
    <div x-data x-init="$watch('$store.config.digit_mk', value => console.log('digit_mk: ', value))"></div>
    <div x-data x-init="$watch('$store.config.semester', value => console.log('semester: ', value))"></div>
    <div x-data x-init="$watch('$store.config.kode_blok', value => console.log('kode_blok: ', value))"></div>

    <div x-data x-init="$watch('$store.config.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
    <div x-data x-init="$watch('$store.config.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div>
</div>
