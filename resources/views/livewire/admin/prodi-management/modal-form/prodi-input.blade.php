<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2">Input Program Studi</h4>

        {{-- 📧 Program Studi Input --}}
        @include('livewire.global.modal-form.input-form', [
            'labelString' => 'Nama Program Studi',
            'modelString' => 'nama_prodi',
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'academic-cap',
            'placeholder' => 'Masukkan nama Program Studi',
            'message' => $errors->first('nama_prodi'),
            'isRequired' => 1,
        ])

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
            'iconString' => 'book-open'
        ])

        {{-- 📧 Kode Program Studi Input --}}
        @include('livewire.global.modal-form.input-form', [
            'labelString' => 'Kode Program Studi',
            'modelString' => 'kode_pr',
            'iconString' => 'hashtag',
            'placeholder' => 'Masukkan 3 huruf Kode Program Studi',
            'message' => $errors->first('kode_pr'),
            'isKode' => 3,
            'isFocusSelect' => 1
        ])

        {{-- 📧 Nama Strata Input --}}
        @include('livewire.global.modal-form.select-form', [
            'labelString' => 'Nama Strata',
            'modelString' => 'nama_strata',
            'xOptions' => ['Sarjana', 'Magister', 'Doktor'],
            'iconString' => 'bookmark-square',
            'placeholder' => 'Pilih Strata...',
            'message' => $errors->first('nama_strata'),
        ])
    </div>
</div>
