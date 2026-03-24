<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div
        class="p-4 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2">
            Input Mata Kuliah</h4>

        {{-- 📧 Mata Kuliah Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            'labelString' => 'Nama Mata Kuliah',
            'modelString' => 'nama_matkul',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan nama Mata Kuliah',
            'message' => $errors->first('nama_matkul'),
            'isRequired' => 1,
        ])

        <template x-if="$store.config?.typeModal == 'mk-prodi'" x-cloak>
            @include('livewire.admin.matkul-management.modal-form.mk-prodi-input')
        </template>

        <template x-if="$store.config?.typeModal == 'mk-jurusan'" x-cloak>
            @include('livewire.admin.matkul-management.modal-form.mk-jurusan-input')
        </template>

        <template x-if="$store.config?.typeModal == 'mk-fakultas'" x-cloak>
            @include('livewire.admin.matkul-management.modal-form.mk-fakultas-input')
        </template>

        <template x-if="$store.config?.typeModal == 'mk-universitas'" x-cloak>
            @include('livewire.admin.matkul-management.modal-form.mk-universitas-input')
        </template>


        <div class="grid sm:grid-cols-6 gap-1">
            <div class="sm:col-span-2">
                @include('livewire.admin.global.modal-form.select-form', [
                    'labelString' => 'Semester',
                    'modelString' => 'semester',
                    'xOptions' => [
                        'Semester 1',
                        'Semester 2',
                        'Semester 3',
                        'Semester 4',
                        'Semester 5',
                        'Semester 6',
                        'Semester 7',
                        'Semester 8',
                    ],
                    'xValues' => [1, 2, 3, 4, 5, 6, 7, 8],
                    'iconString' => 'bookmark-square',
                    'placeholder' => 'Pilih Semester...',
                    'message' => $errors->first('semester'),
                    'isRequired' => 1,
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.admin.global.modal-form.select-form', [
                    'labelString' => 'Kategori Blok',
                    'modelString' => 'kode_blok',
                    'xOptions' => ['Reguler', 'Kerja Praktik / Tugas Akhir'],
                    'xValues' => [1, 0],
                    'iconString' => 'tag',
                    'placeholder' => 'Pilih kategori...',
                    'message' => $errors->first('kode_blok'),
                ])
            </div>

            <div class="sm:col-span-2">
                @include('livewire.admin.global.modal-form.select-form', [
                    'labelString' => 'Wajib / Pilihan',
                    'modelString' => 'is_wajib',
                    'xOptions' => ['Wajib', 'Pilihan'],
                    'xValues' => [1, 0],
                    'iconString' => 'tag',
                    'placeholder' => 'Wajib / Pilihan',
                    'message' => $errors->first('is_wajib'),
                ])
            </div>
        </div>

        <div class="grid sm:grid-cols-8 gap-4">
            <div class="sm:col-span-5">
                @include('livewire.admin.global.modal-form.select-form', [
                    'labelString' => 'Tipe SKS',
                    'modelString' => 'tipe_sks',
                    'xOptions' => ['Tatap Muka', 'Praktikum', 'Praktek Lapangan', 'Simulasi'],
                    'xValues' => [1, 2, 3, 4],
                    'iconString' => 'bookmark-square',
                    'placeholder' => 'Pilih tipe SKS...',
                    'message' => $errors->first('tipe_sks'),
                ])
            </div>
            <div class="sm:col-span-3">
                @include('livewire.admin.global.modal-form.input-form', [
                    'labelString' => 'SKS',
                    'modelString' => 'sks_kuliah',
                    'numberOnly' => 1,
                    'maxlength' => 1,
                    'noZero' => 1,
                    'iconString' => 'identification',
                    'placeholder' => 'SKS',
                    'message' => $errors->first('sks_kuliah'),
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>

        {{-- <div x-data x-text="$store.config.typeModal"></div> --}}

        <div x-data x-init="$watch('$store.config.nama_matkul', value => console.log('nama_matkul: ', value))"></div>

        <div x-data x-init="$watch('$store.config.prodi_id', value => console.log('prodi_id: ', value))"></div>
        <div x-data x-init="$watch('$store.config.kode_pr', value => console.log('kode_pr: ', value))"></div>
        <div x-data x-init="$watch('$store.config.selected_kode_pr', value => console.log('selected_kode_pr: ', value))"></div>

        <div x-data x-init="$watch('$store.config.digit_semester', value => console.log('digit_semester: ', value))"></div>
        <div x-data x-init="$watch('$store.config.digit_mk', value => console.log('digit_mk: ', value))"></div>
        <div x-data x-init="$watch('$store.config.semester', value => console.log('semester: ', value))"></div>
        <div x-data x-init="$watch('$store.config.kode_blok', value => console.log('kode_blok: ', value))"></div>

        <div x-data x-init="$watch('$store.config.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
        <div x-data x-init="$watch('$store.config.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div>

    </div>
</div>
