<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'labelString' => 'Kode Mata Kuliah',
                    'kodeString' => 'fakultas_kode',
                    'placeholder' => '---',
                    'iconString' => 'building-library'
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
                    'wireLoadingParent' => 'selectFakultas, resetFakultasInput, selectFakultasForFilter, resetFakultasFilter',
                ])
            </div>
        </div>
        @error('digit_mk')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('digit_mk') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'mk',
        'xResults' => $fakultasResults,
        'selectX' => 'selectFakultas',
        'modelString' => 'nama_fakultas_search',
        'resetXInput' => 'resetFakultasInput()',
        'typeXString' => 'fakultas',
        'nameXString' => 'Fakultas',
        'idString' => 'fakultas_id',
        'kodeString' => 'fakultas_kode',
        'searchString' => 'fakultas_search',
        'nameSearchString' => 'fakultasNameSearch',
        'fetchString' => 'fetchFakultas',
        'iconString' => 'building-library',
        'wireLoading' => 'fetchFakultas'
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
        'parentIdString' => 'fakultas_id',
        'nameXParent' => 'Fakultas',
        'wireLoading' => 'fetchProdi',
        'wireLoadingParent' => 'selectFakultas, resetFakultasInput, selectFakultasForFilter, resetFakultasFilter',
    ])
</div>
