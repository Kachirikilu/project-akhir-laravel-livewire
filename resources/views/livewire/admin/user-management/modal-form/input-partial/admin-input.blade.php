{{-- ****************************************************** --}}
{{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Personal Information</h4>

    {{-- 👤 Nama Input --}}
    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'nameXString' => 'Full Name',
        'modelString' => 'name',
        'iconString' => 'user-circle',
        'placeholder' => 'Masukkan Nama Lengkap',
        'message' => $errors->first('name')
    ])

    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'nameXString' => 'Nomor Induk Pegawai (NIP)',
        'modelString' => 'nip',
        'numberOnly' => 1,
        'maxlength' => 20,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NIP',
        'message' => $errors->first('nip')
    ])
    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'nameXString' => 'Nomor Induk Tenaga Kerja (NITK)',
        'modelString' => 'nitk',
        'numberOnly' => 1,
        'maxlength' => 20,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NITK',
        'message' => $errors->first('nitk'),
        'isRequired' => 0,
    ])

    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'user',
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr_search',

        'idString' => 'pr_id',
        'itemsAllString' => 'pr_items',

        'resetXInput' => 'resetPrInput()',
        'typeXString' => 'prodi',
        'typeX2String' => 'jurusan',
        'typeX3String' => 'fakultas',

        'nameXString' => 'Program Studi',
        'nameSearchString' => 'prNameSearch',
        'fetchString' => 'fetchPr',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchPr'
    ])

    {{-- 📧 Status Input --}}
    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'user',
        'nameXString' => 'Status',
        'modelString' => 'status',
        'xOptions' => [
            'Aktif', // Hijau (Produktif)
            'Tugas Belajar', // Kuning (Transisi/Sementara)
            'Mutasi', // Kuning (Transisi/Sementara)
            'Cuti Luar Tanggungan', // Kuning (Transisi/Sementara)
            'Resign', // Orange (Keluar Prosedural)
            'Pensiun', // Orange (Keluar Prosedural)
            'Diberhentikan', // Merah (Masalah/Sanksi)
            'Meninggal Dunia', // Merah (Permanen)
        ],
        // 'typeString' => 'text',
        // 'colorIcon' => $colorIcon,
        'iconString' => 'tag',
        'placeholder' => 'Pilih Status...',
        'message' => $errors->first('status'),
    ])

</div>
