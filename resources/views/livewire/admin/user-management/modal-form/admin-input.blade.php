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
            'labelString' => 'Nomor Induk Pegawai (NIP)',
            'modelString' => 'nip',
            'numberOnly' => 1,
            'maxlength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIP',
            'message' => $errors->first('nip'),
            'isRequired' => 1,
        ])
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nomor Induk Tenaga Kerja (NITK)',
            'modelString' => 'nitk',
            'numberOnly' => 1,
            'maxlength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NITK',
            'message' => $errors->first('nitk'),
            'isRequired' => 0,
        ])

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
                'Aktif',                  // Hijau (Produktif)
                'Tugas Belajar',          // Kuning (Transisi/Sementara)
                'Mutasi',                 // Kuning (Transisi/Sementara)
                'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                'Resign',                 // Orange (Keluar Prosedural)
                'Pensiun',                // Orange (Keluar Prosedural)
                'Diberhentikan',          // Merah (Masalah/Sanksi)
                'Meninggal Dunia'         // Merah (Permanen)
            ],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])

    </div>
</div>
