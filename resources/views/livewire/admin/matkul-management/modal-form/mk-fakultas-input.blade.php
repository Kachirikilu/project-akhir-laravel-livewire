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

        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2" x-data="{ kode: @entangle('selected_kode_jr').live }">
                <label class="block text-sm font-medium">Kode Mata Kuliah</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <flux:icon icon="academic-cap" variant="mini" x-bind:class="$store.config?.colorIcon" />
                    </div>

                    <input type="text" x-bind:value="kode || '---'" readonly placeholder="---"
                        class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
                </div>
            </div>


            <div class="sm:col-span-2" 
                x-data="{}" 
                x-effect="
                    if ($store.config) {
                        let sem = parseInt($store.config.semester);
                        let blok = parseInt($store.config.kode_blok);
                        
                        if (!sem) {
                            $store.config.digit_semester = '';
                        } else if (blok === 0) {
                            $store.config.digit_semester = Math.ceil(sem / 2).toString() + '0';
                        } else {
                            let tahun = Math.ceil(sem / 2);
                            let tipe = (sem % 2 !== 0) ? '1' : '2';
                            $store.config.digit_semester = tahun.toString() + tipe;
                        }
                    }
                ">
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <flux:icon icon="variable" variant="mini" x-bind:class="$store.config?.colorIcon" />
                    </div>
                    
                    <input type="text" 
                        x-bind:value="$store.config?.digit_semester || '--'" 
                        readonly 
                        placeholder="--"
                        class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
                </div>
            </div>

            <div class="sm:col-span-2">
                @include('livewire.admin.global.modal-form.input-form', [
                    'labelString' => 'Urutan Mata Kuliah',
                    'modelString' => 'digit_mk',
                    'numberOnly' => 1,
                    'maxlength' => 2,
                    'iconString' => 'identification',
                    'placeholder' => 'Contoh: 07',
                    'message' => $errors->first('digit_mk'),
                    'isRequired' => 1,
                ])
            </div>
        </div>

        @include('livewire.admin.global.modal-form.search-input-form', [
            'xResults' => $jurusan_results,
            'selectX' => 'selectJurusan',
            'modelString' => 'nama_jurusan',
            'resetXInput' => 'resetJurusanInput()',
            'typeXString' => 'jurusan',
            'nameXString' => 'Jurusan',
            'idString' => 'jurusan_id',
            'kodeString' => 'selected_kode_jr',
            'searchString' => 'jurusan_search',
            'nameSearchString' => 'jurusan_name_search',
            'fetchString' => 'fetchJurusan',
            'iconString' => 'book-open'
        ])

        @include('livewire.admin.global.modal-form.search-input-array-form', [
            'xResults' => $prodi_results,
            'selectX' => 'selectProdi',
            'modelString' => 'nama_prodi',
            'resetXInput' => 'resetProdiInput()',
            'typeXString' => 'prodi',
            'nameXString' => 'Program Studi',
            'noName' => 1,
            'idString' => 'prodi_id_array',
            'kodeString' => 'selected_kode_pr_array',
            'searchString' => 'prodi_search',
            'nameSearchString' => 'prodi_name_search',
            'fetchString' => 'fetchProdi',
            'iconString' => 'academic-cap',
        ])

        <div class="grid sm:grid-cols-8 gap-4">
            <div class="sm:col-span-5">
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
            <div class="sm:col-span-3">
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
        </div>


        <div class="grid sm:grid-cols-8 gap-4">
            <div class="sm:col-span-5">
                @include('livewire.admin.global.modal-form.select-form', [
                    'labelString' => 'Tipe SKS',
                    'modelString' => 'tipe_sks',
                    'xOptions' => ['Tatap Muka', 'Praktikum', 'Praktek Lapangan', 'Simulasi'],
                    'xValues' => [0, 1, 2, 3],
                    'iconString' => 'bookmark-square',
                    'placeholder' => 'Pilih tipe SKS...',
                    'message' => $errors->first('tipe_sks'),
                    'isRequired' => 1,
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
                    'isRequired' => 1,
                ])
            </div>
        </div>

        <div x-data x-init="$watch('$store.config.nama_matkul', value => console.log('nama_matkul: ', value))"></div>

        <div x-data x-init="$watch('$store.config.prodi_id', value => console.log('prodi_id: ', value))"></div>
        <div x-data x-init="$watch('$store.config.kode_pr', value => console.log('kode_pr: ', value))"></div>

        <div x-data x-init="$watch('$store.config.selected_kode_pr', value => console.log('selected_kode_pr: ', value))"></div>
        <div x-data x-init="$watch('$store.config.selected_kode_jr', value => console.log('selected_kode_jr: ', value))"></div>

        <div x-data x-init="$watch('$store.config.digit_semester', value => console.log('digit_semester: ', value))"></div>
        <div x-data x-init="$watch('$store.config.digit_mk', value => console.log('digit_mk: ', value))"></div>
        <div x-data x-init="$watch('$store.config.semester', value => console.log('semester: ', value))"></div>
        <div x-data x-init="$watch('$store.config.kode_blok', value => console.log('kode_blok: ', value))"></div>

        <div x-data x-init="$watch('$store.config.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
        <div x-data x-init="$watch('$store.config.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div>

    </div>
</div>
