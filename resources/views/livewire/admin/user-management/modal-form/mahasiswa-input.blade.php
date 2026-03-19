<div>
    {{-- ****************************************************** --}}
    {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Personal Information</h4>

        {{-- 👤 Nama Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Full Name',
            'modelString' => 'name',
            'iconString' => 'user-circle',
            'placeholder' => 'Masukkan Nama Lengkap',
            'message' => $errors->first('name'),
            'isRequired' => 1,
        ])

        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nomor Induk Mahasiswa (NIM)',
            'modelString' => 'nim',
            'numberOnly' => 1,
            'maxlength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIM',
            'message' => $errors->first('nim'),
            'isRequired' => 1,
        ])

        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Tahun Angkatan',
            'modelString' => 'tahun_angkatan',
            'numberOnly' => 1,
            'maxlength' => 4,
            'iconString' => 'calendar-days',
            'placeholder' => 'Masukkan Tahun Angkatan',
            'message' => $errors->first('tahun_angkatan'),
            'isRequired' => 1,
        ])


        {{-- @include('livewire.admin.user-management.modal-form.partial.prodi-input-form') --}}
        @include('livewire.admin.global.modal-form.search-input-form', [
            'xResults' => $prodi_results,
            'selectX' => 'selectProdi',
            'modelString' => 'nama_prodi',
            'resetXInput' => 'resetProdiInput()',
            'typeXString' => 'prodi',
            'nameXString' => 'Program Studi',
            'noName' => 1,
            'idString' => 'prodi_id',
            'searchString' => 'prodi_search',
            'nameSearchString' => 'prodi_name_search',
            'fetchString' => 'fetchProdi',
            'iconString' => 'academic-cap'
        ])

        {{-- 📧 Status Input --}}
        @include('livewire.admin.global.modal-form.select-form', [
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
            'message' => $errors->first('status'),
        ])

    </div>
</div>
