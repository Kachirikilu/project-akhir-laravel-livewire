<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div
        class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Mata Kuliah</h4>

        {{-- 📧 Mata Kuliah Input --}}
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'mk',
            'labelString' => 'Nama Mata Kuliah',
            'modelString' => 'nama_matkul',
            'iconString' => 'rectangle-stack',
            'placeholder' => 'Masukkan nama Mata Kuliah',
            'message' => $errors->first('nama_matkul')
        ])

        <div class="relative">


            @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addMK, editMK'])

            <template x-if="$store.mk?.typeModal == 1" x-cloak>
                @include('livewire.staff.matkul-management.modal-form.input-partial.mk-prodi-input')
            </template>

            <template x-if="$store.mk?.typeModal == 2" x-cloak>
                @include('livewire.staff.matkul-management.modal-form.input-partial.mk-jurusan-input')
            </template>

            <template x-if="$store.mk?.typeModal == 3" x-cloak>
                @include('livewire.staff.matkul-management.modal-form.input-partial.mk-fakultas-input')
            </template>

            <template x-if="$store.mk?.typeModal == 4" x-cloak>
                @include('livewire.staff.matkul-management.modal-form.input-partial.mk-universitas-input')
            </template>
        </div>


        <div class="grid sm:grid-cols-6 gap-1">
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'mk',
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
                    'message' => $errors->first('semester')
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'mk',
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
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'mk',
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
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'mk',
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
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'mk',
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

        {{-- <div x-data x-init="$watch('$store.mk.nama_matkul', value => console.log('nama_matkul: ', value))"></div> --}}
    </div>
</div>
