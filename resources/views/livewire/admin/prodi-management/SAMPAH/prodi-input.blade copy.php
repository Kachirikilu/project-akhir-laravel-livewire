<div>
    {{-- ****************************************************** --}}
    {{-- 1. INPUT PROGRAM STUDI --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Input Program Studi</h4>

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

        {{-- 📧 Nama Strata Input --}}
        @include('livewire.global.modal-form.select-form', [
            'labelString' => 'Nama Strata',
            'modelString' => 'nama_strata',
            'xOptions' => ['Sarjana', 'Magister', 'Doktor'],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'bookmark-square',
            'placeholder' => 'Pilih Strata...',
            'message' => $errors->first('nama_strata'),
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
            'searchString' => 'jurusan_search',
            'nameSearchString' => 'jurusanNameSearch',
            'fetchString' => 'fetchJurusan',
            'iconString' => 'book-open'
        ])
    </div>
</div>
