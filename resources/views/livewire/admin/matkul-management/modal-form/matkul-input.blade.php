<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white dark:bg-neutral-800 shadow-sm rounded-lg border border-gray-100 dark:border-neutral-700 space-y-4 transition-colors duration-300">
        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 border-b dark:border-neutral-700 pb-2">Input Program Studi</h4>

        {{-- 📧 Program Studi Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            'labelString' => 'Nama Program Studi',
            'modelString' => 'nama_prodi',
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'academic-cap',
            'placeholder' => 'Masukkan nama Program Studi',
            'message' => $errors->first('nama_prodi'),
            'isRequired' => 1,
        ])

        @include('livewire.admin.global.modal-form.search-input-form', [
            'xResults' => $jurusan_results,
            'selectX' => 'selectJurusan',
            'modelString' => 'nama_jurusan',
            'resetXInput' => 'resetJurusanInput()',
            'typeXString' => 'jurusan',
            'nameXString' => 'Jurusan',
            'idString' => 'jurusan_id',
            'searchString' => 'jurusan_search',
            'nameSearchString' => 'jurusan_name_search',
            'fetchString' => 'fetchJurusan',
            'iconString' => 'book-open'
        ])

        {{-- 📧 Kode Program Studi Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            'labelString' => 'Kode Program Studi',
            'modelString' => 'kode_pr',
            'iconString' => 'hashtag',
            'placeholder' => 'Masukkan 3 huruf Kode Program Studi',
            'message' => $errors->first('kode_pr'),
            'isKode' => 3
        ])

        {{-- 📧 Nama Strata Input --}}
        @include('livewire.admin.global.modal-form.select-form', [
            'labelString' => 'Nama Strata',
            'modelString' => 'nama_strata',
            'xOptions' => ['Sarjana', 'Magister', 'Doktor'],
            'iconString' => 'bookmark-square',
            'placeholder' => 'Pilih Strata...',
            'message' => $errors->first('nama_strata'),
        ])
    </div>
</div>
