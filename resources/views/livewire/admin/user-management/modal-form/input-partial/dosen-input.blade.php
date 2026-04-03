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
        'labelString' => 'Full Name',
        'modelString' => 'name',
        'iconString' => 'user-circle',
        'placeholder' => 'Masukkan Nama Lengkap',
        'message' => $errors->first('name'),
        'isRequired' => 1,
    ])

    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'labelString' => 'Nomor Induk Pegawai (NIP)',
        'modelString' => 'nip',
        'numberOnly' => 1,
        'maxlength' => 20,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NIP',
        'message' => $errors->first('nip'),
        'isRequired' => 1,
    ])
    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'labelString' => 'Nomor Induk Dosen Nasional (NIDN)',
        'modelString' => 'nidn',
        'numberOnly' => 1,
        'maxlength' => 20,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NIDN',
        'message' => $errors->first('nidn'),
        'isRequired' => 0,
    ])
    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'labelString' => 'Nomor Induk Dosen Khusus (NIDK)',
        'modelString' => 'nidk',
        'numberOnly' => 1,
        'maxlength' => 20,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NIDK',
        'message' => $errors->first('nidk'),
        'isRequired' => 0,
    ])

    {{-- @include('livewire.admin.user-management.modal-form.partial.prodi-input-form') --}}
    @include('livewire.global.modal-form.search-input-form', [
        'alpine' => 'user',
        'xResults' => $prodiResults,
        'selectX' => 'selectProdi',
        'modelString' => 'nama_prodi_search',
        'resetXInput' => 'resetProdiInput()',
        'typeXString' => 'prodi',
        'nameXString' => 'Program Studi',
        'noName' => 1,
        'idString' => 'prodi_id',
        'kodeString' => 'prodi_kode',
        'searchString' => 'prodi_search',
        'nameSearchString' => 'prodiNameSearch',
        'fetchString' => 'fetchProdi',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchProdi',
    ])

    {{-- 📧 Status Input --}}
    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'user',
        'labelString' => 'Status',
        'modelString' => 'status',
        'xOptions' => [
            'Aktif', // Hijau (Produktif)
            'Tugas Belajar', // Kuning (Transisi/Studi)
            'Izin Belajar', // Kuning (Transisi/Studi)
            'Cuti Sabatika', // Kuning (Transisi/Riset)
            'Alih Tugas', // Orange (Perubahan Jabatan)
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
        'isRequired' => 0,
    ])

</div>
