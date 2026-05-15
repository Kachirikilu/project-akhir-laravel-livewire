<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Mahasiswa Kelas</h4>

    @include('livewire.global.modal-form.search-input-array-form', [
        'alpine' => 'jadwal',
        'xResults' => $mahasiswaResults,
        'selectX' => 'selectMahasiswaArray',
        'modelString' => 'nama_mahasiswa_search',

        'idString' => 'mahasiswa_id_array',
        'itemsAllString' => 'mahasiswa_items_array',

        'typeXString' => 'name',
        'typeX2String' => 'prodi',
        'typeX3String' => 'wilayah',
        'typeX4String' => 'angkatan_full',
        'typeX5String' => 'status_full',

        'nameXString' => 'Mahasiswa',
        'nameSearchString' => 'mahasiswaNameSearch',
        'fetchString' => 'fetchMahasiswa',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchMahasiswa'
    ])
</div>
