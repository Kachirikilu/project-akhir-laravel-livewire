<div>
    {{-- ****************************************************** --}}
    {{-- 2. INPUT JURUSAN --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2">Input Jurusan</h4>

        {{-- 📧 Jurusan Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nama Jurusan',
            'modelString' => 'nama_jurusan',
            // 'typeString' => 'text',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan nama Jurusan',
            'message' => $errors->first('nama_jurusan'),
            'isRequired' => 1
        ])

        @include('livewire.admin.global.modal-form.search-input-form', [
            'xResults' => $fakultas_results,
            'selectX' => 'selectFakultas',
            'modelString' => 'nama_fakultas',
            'resetXInput' => 'resetFakultasInput()',
            'typeXString' => 'fakultas',
            'nameXString' => 'Fakultas',
            'idString' => 'fakultas_id',
            'kodeString' => 'selected_kode_fk',
            'searchString' => 'fakultas_search',
            'nameSearchString' => 'fakultas_name_search',
            'fetchString' => 'fetchFakultas',
            'iconString' => 'building-library'
        ])

        {{-- 📧 Kode Jurusan Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            'labelString' => 'Kode Jurusan',
            'modelString' => 'kode_jr',
            'iconString' => 'hashtag',
            'placeholder' => 'Masukkan 3 huruf Kode Jurusan',
            'message' => $errors->first('kode_jr'),
            'isKode' => 3
        ])

    </div>
</div>