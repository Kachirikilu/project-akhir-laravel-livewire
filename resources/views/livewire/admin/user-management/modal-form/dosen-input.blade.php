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
            'placeholder' => 'Masukkan NIP',
            'message' => $errors->first('nip'),
            'isRequired' => 1,
        ])
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nomor Induk Dosen Nasional (NIDN)',
            'modelString' => 'nidn',
            'numberOnly' => 1,
            'maxlength' => 20,
            'placeholder' => 'Masukkan NIDN',
            'message' => $errors->first('nidn'),
            'isRequired' => 0,
        ])
        @include('livewire.admin.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'labelString' => 'Nomor Induk Dosen Khusus (NIDK)',
            'modelString' => 'nidk',
            'numberOnly' => 1,
            'maxlength' => 20,
            'placeholder' => 'Masukkan NIDK',
            'message' => $errors->first('nidk'),
            'isRequired' => 0,
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

    </div>
</div>
