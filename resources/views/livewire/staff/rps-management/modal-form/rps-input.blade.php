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
            Input Rencana Pembelajaran Semester</h4>

        {{-- 📧 Mata Kuliah Input --}}
        {{-- @include('livewire.global.modal-form.input-form', [
            'alpine' => 'rps',
            'labelString' => 'Nama Mata Kuliah',
            'modelString' => 'nama_matkul',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan nama Mata Kuliah',
            'message' => $errors->first('nama_matkul'),
            'isRequired' => 1,
        ]) --}}

        <div class="relative">


            <div wire:loading wire:target="addRPS, editRPS"
                class="absolute inset-0 z-[100] flex flex-col items-center justify-center bg-[var(--second-table-color)]/60 backdrop-blur-[2px] rounded-lg">

                <div class="h-full flex flex-col items-center justify-center">
                    <svg class="animate-spin h-10 w-10 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <p class="mt-3 text-sm font-semibold text-[var(--focus-color)] tracking-wide animate-pulse">
                        Sedang Memproses...
                    </p>
                </div>
            </div>



            <div class="space-y-4">

                @include('livewire.global.modal-form.search-input-form', [
                    'alpine' => 'rps',
                    'xResults' => $matkulResults,
                    'selectX' => 'selectMatkul',
                    'modelString' => 'nama_matkul_array',
                    'resetXInput' => 'resetMatkulInput()',
                    'typeXString' => 'matkul',
                    'nameXString' => 'Mata Kuliah',
                    'noName' => 1,
                    'idString' => 'matkul_id',
                    'kodeString' => 'matkul_kode',
                    'searchString' => 'matkul_search',
                    'nameSearchString' => 'matkulNameSearch',
                    'fetchString' => 'fetchMatkul',
                    'iconString' => 'academic-cap',
                    'wireLoading' => 'fetchMatkul',
                ])


                <div>
                    <div class="grid sm:grid-cols-6 gap-1 items-end">

                        <div class="sm:col-span-4">
                            @include('livewire.staff.rps-management.modal-form.partial.kode-rps', [
                                'kodeString' => 'matkul_kode',
                            ])
                        </div>

                        {{-- <div class="sm:col-span-2">
                            @include('livewire.staff.rps-management.modal-form.partial.digit-semester')
                        </div> --}}

                        <div class="sm:col-span-2">
                            @include('livewire.staff.rps-management.modal-form.partial.digit-akademik')
                        </div>
                    </div>
                    {{-- @error('digit_mk')
                        <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('digit_mk') }}</span>
                    @enderror --}}
                </div>

                <div class="space-y-4">
                    <div class="grid sm:grid-cols-4 gap-1 items-end">

                        <div class="sm:col-span-2">
                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'rps',
                                'labelString' => 'Tahun Akademik',
                                'modelString' => 'tahun_akademik_1',
                                'numberOnly' => 1,
                                'maxlength' => 4,
                                'iconString' => 'identification',
                                'placeholder' => 'Contoh: 2025',
                                'isRequired' => 1,
                                'isFocusSelect' => 1,
                            ])
                        </div>
                        <div class="sm:col-span-2">
                            @include('livewire.staff.rps-management.modal-form.partial.tahun-akademik-2')
                        </div>
                    </div>
                    @error('tahun_akademik')
                        <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('tahun_akademik') }}</span>
                    @enderror
                </div>

            </div>




        </div>


        <div class="grid sm:grid-cols-6 gap-1">
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'rps',
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
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'rps',
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
                    'alpine' => 'rps',
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
                    'alpine' => 'rps',
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
                    'alpine' => 'rps',
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
