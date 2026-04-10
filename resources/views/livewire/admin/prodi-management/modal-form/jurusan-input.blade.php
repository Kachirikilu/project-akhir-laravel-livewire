{{-- ****************************************************** --}}
{{-- 2. INPUT JURUSAN --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Jurusan</h4>

    {{-- 📧 Jurusan Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        // 'colorIcon' => $colorIcon,
        'labelString' => 'Nama Jurusan',
        'modelString' => 'nama_jr',
        // 'typeString' => 'text',
        'iconString' => 'book-open',
        'placeholder' => 'Masukkan nama Jurusan',
        'message' => $errors->first('nama_jr')
    ])

    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $fakultasResults,
        'selectX' => 'selectFakultas',
        'modelString' => 'nama_fk_search',

        'idString' => 'fk_id',
        'itemsAllString' => 'fakultas_items',

        'resetXInput' => 'resetFakultasInput()',
        'typeXString' => 'fakultas',

        'nameXString' => 'Fakultas',
        'searchString' => 'fakultas_search',
        'nameSearchString' => 'fakultasNameSearch',
        'fetchString' => 'fetchFakultas',
        'iconString' => 'building-library',
        'wireLoading' => 'fetchFakultas'
    ])

    {{-- 📧 Kode Jurusan Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'labelString' => 'Kode Jurusan',
        'modelString' => 'kode_jr',
        'iconString' => 'hashtag',
        'placeholder' => 'Masukkan 3 huruf Kode Jurusan',
        'message' => $errors->first('kode_jr'),
        'isKode' => 3,
        'isFocusSelect' => 1,
    ])

</div>
