<div>
    {{-- ****************************************************** --}}
    {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2">Personal Information</h4>

        {{-- 👤 Nama Input --}}
        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Full Name',
            'modelString' => 'name',
            'iconString' => 'user-circle',
            'placeholder' => 'Masukkan Nama Lengkap',
            'message' => $errors->first('name'),
            'isRequired' => 1
        ])

        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nomor Induk Mahasiswa (NIM)',
            'modelString' => 'nim',
            'numberOnly' => 1,
            'maxlength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIM',
            'message' => $errors->first('nim'),
            'isRequired' => 1
        ])

        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Tahun Angkatan',
            'modelString' => 'tahun_angkatan',
            'numberOnly' => 1,
            'maxlength' => 4,
            'iconString' => 'calendar-days',
            'placeholder' => 'Masukkan Tahun Angkatan',
            'message' => $errors->first('tahun_angkatan'),
            'isRequired' => 1
        ])


        {{-- @include('livewire.admin.user-management.modal-form.partial.prodi-input-form') --}}
        @include('livewire.global.modal-form.search-input-form', [
            'xResults' => $prodiResults,
            'selectX' => 'selectProdi',
            'modelString' => 'nama_prodi',
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
            'wireLoading' => 'fetchProdi'
        ])

        {{-- 📧 Status Input --}}
        @include('livewire.global.modal-form.select-form', [
            'labelString' => 'Status',
            'modelString' => 'status',
            'xOptions' => [
                'Aktif',                  // Hijau (Aktif Kuliah)
                'Lulus',                  // Biru (Output Positif)
                'Cuti',                   // Kuning (Jeda Resmi)
                'Pindah',                 // Kuning (Transisi Keluar)
                'Non-Aktif',              // Orange (Masalah Administrasi)
                'Mengundurkan Diri',      // Orange (Keluar Prosedural)
                'Drop Out',               // Merah (Masalah Akademik/Sanksi)
                'Hilang',                 // Merah (Tanpa Kabar/Ghaib)
                'Meninggal Dunia'         // Merah (Permanen)
            ],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status')
        ])

    </div>
</div>
