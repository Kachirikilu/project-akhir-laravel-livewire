<div>
    {{-- ****************************************************** --}}
    {{-- 2. INPUT JURUSAN --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white dark:bg-neutral-800 shadow-sm rounded-lg border border-gray-100 dark:border-neutral-700 space-y-4 transition-colors duration-300">
        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 border-b dark:border-neutral-700 pb-2">Input Jurusan</h4>

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