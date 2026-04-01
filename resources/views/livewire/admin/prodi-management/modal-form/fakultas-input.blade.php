<div>
    {{-- ****************************************************** --}}
    {{-- 3. INPUT FAKULTAS --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2">Input Fakultas</h4>

        {{-- 📧 Fakultas Input --}}
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'prodi',
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nama Fakultas',
            'modelString' => 'nama_fakultas',
            // 'typeString' => 'text',
            'iconString' => 'building-library',
            'placeholder' => 'Masukkan nama Fakultas',
            'message' => $errors->first('nama_fakultas'),
            'isRequired' => 1
        ])

        {{-- 📧 Kode Fakultas Input --}}
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'prodi',
            'labelString' => 'Kode Fakultas',
            'modelString' => 'kode_fk',
            'iconString' => 'hashtag',
            'placeholder' => 'Masukkan 3 huruf Kode Fakultas',
            'message' => $errors->first('kode_fk'),
            'isKode' => 3,
            'isRequired' => 1,
            'isFocusSelect' => 1
        ])
    </div>
</div>

