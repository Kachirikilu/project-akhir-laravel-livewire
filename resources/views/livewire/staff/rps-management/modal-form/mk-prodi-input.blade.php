<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.staff.matkul-management.modal-form.partial.kode-mk', ['kodeString' => 'prodi_kode'])
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
                    'isFocusSelect' => 1
                ])
            </div>
        </div>
        @error('digit_mk')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('digit_mk') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'mk',
        'xResults' => $prodiResults,
        'selectX' => 'selectProdi',
        'modelString' => 'nama_prodi_array',
        'resetXInput' => 'resetProdiInput()',
        'typeXString' => 'prodi',
        'nameXString' => 'Program Studi',
        'noName' => 1,
        'idString' => 'prodi_id',
        'kodeString' => 'prodi_kode',
        'searchString' => 'prodi_search',
        'nameSearchString' => 'prodiNameSearch',
        'fetchString' => 'fetchProdi',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchProdi'
    ])


    {{-- <div x-data x-init="$watch('$store.mk.nama_matkul', value => console.log('nama_matkul: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.prodi_id', value => console.log('prodi_id: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.prodi_kode', value => console.log('kode_pr: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.prodi_kode', value => console.log('prodi_kode: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.digit_semester', value => console.log('digit_semester: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.digit_mk', value => console.log('digit_mk: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.semester', value => console.log('semester: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.kode_blok', value => console.log('kode_blok: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div> --}}
</div>
