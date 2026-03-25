<div>
    {{-- ****************************************************** --}}
    {{-- 2. INPUT JURUSAN --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Input Jurusan</h4>

        {{-- 📧 Jurusan Input --}}
        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nama Jurusan',
            'modelString' => 'nama_jurusan',
            // 'typeString' => 'text',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan nama Jurusan',
            'message' => $errors->first('nama_jurusan'),
            'isRequired' => 1
        ])

        @include('livewire.global.modal-form.search-input-form', [
            'xResults' => $fakultasResults,
            'selectX' => 'selectFakultas',
            'modelString' => 'nama_fakultas',
            'resetXInput' => 'resetFakultasInput()',
            'typeXString' => 'fakultas',
            'nameXString' => 'Fakultas',
            'idString' => 'fakultas_id',
            'searchString' => 'fakultas_search',
            'nameSearchString' => 'fakultasNameSearch',
            'fetchString' => 'fetchFakultas',
            'iconString' => 'building-library'
        ])
    </div>
</div>