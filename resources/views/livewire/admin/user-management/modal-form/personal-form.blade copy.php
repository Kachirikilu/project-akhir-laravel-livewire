<div>
    {{-- ****************************************************** --}}
    {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Personal Information</h4>

        {{-- 👤 Nama Input --}}
        @include('livewire.admin.user-management.modal-form.partial.input', [
            'colorIcon' => $colorIcon,
            'labelString' => 'Full Name',
            'modelString' => 'name',
            'placeholder' => 'Masukkan Nama Lengkap',
            'message' => $errors->first('name'),
            'isRequired' => 1,
        ])

        {{-- Input Khusus Berdasarkan Role Type --}}
        @if ($roleType === 'admin' || $roleType === 'dosen')
            {{-- 🆔 NIP Input (Admin/Dosen) --}}

            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Nomor Induk Pegawai (NIP)',
                'modelString' => 'nip',
                'placeholder' => 'Masukkan NIP',
                'message' => $errors->first('nip'),
                'isRequired' => 1
            ])

            @if ($roleType == 'admin')
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Tenaga Kerja (NITK)',
                    'modelString' => 'nitk',
                    'placeholder' => 'Masukkan NITK',
                    'message' => $errors->first('nitk')
                ])
            @else
                {{-- 🆔 NIDN Input (Dosen) --}}
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Dosen Nasional (NIDN)',
                    'modelString' => 'nidn',
                    'placeholder' => 'Masukkan NIDN',
                    'message' => $errors->first('nidn')
                ])

                {{-- 🆔 NIDK Input (Dosen) --}}
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Dosen Khusus (NIDK)',
                    'modelString' => 'nidk',
                    'placeholder' => 'Masukkan NIDK',
                    'message' => $errors->first('nidk')
                ])
            @endif
        @elseif($roleType === 'mahasiswa')
            {{-- 🔢 NIM Input (Mahasiswa) --}}
            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Nomor Induk Mahasiswa (NIM)',
                'modelString' => 'nim',
                'placeholder' => 'Masukkan NIM',
                'message' => $errors->first('nim'),
                'isRequired' => 1
            ])

            {{-- 📅 Tahun Angkatan Input (Mahasiswa) --}}
            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Entry Year',
                'modelString' => 'tahun_angkatan',
                'typeString' => 'number',
                'placeholder' => 'Contoh: 2022',
                'message' => $errors->first('tahun_angkatan'),
                'isRequired' => 1
            ])

        @endif

        @include('livewire.admin.user-management.modal-form.prodi-input-form')

    </div>
</div>



        {{-- Input Khusus Berdasarkan Role Type --}}
        @if ($roleType === 'admin' || $roleType === 'dosen')
            {{-- 🆔 NIP Input (Admin/Dosen) --}}

            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Nomor Induk Pegawai (NIP)',
                'modelString' => 'nip',
                'numberOnly' => 1,
                'maxlength' => 20,
                'placeholder' => 'Masukkan NIP',
                'message' => $errors->first('nip'),
                'isRequired' => 1,
            ])

            @if ($roleType == 'admin')
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Tenaga Kerja (NITK)',
                    'modelString' => 'nitk',
                    'numberOnly' => 1,
                    'maxlength' => 20,
                    'placeholder' => 'Masukkan NITK',
                    'message' => $errors->first('nitk'),
                ])
            @else
                {{-- 🆔 NIDN Input (Dosen) --}}
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Dosen Nasional (NIDN)',
                    'modelString' => 'nidn',
                    'numberOnly' => 1,
                    'maxlength' => 20,
                    'placeholder' => 'Masukkan NIDN',
                    'message' => $errors->first('nidn'),
                ])

                {{-- 🆔 NIDK Input (Dosen) --}}
                @include('livewire.admin.user-management.modal-form.partial.input', [
                    'colorIcon' => $colorIcon,
                    'labelString' => 'Nomor Induk Dosen Khusus (NIDK)',
                    'modelString' => 'nidk',
                    'numberOnly' => 1,
                    'maxlength' => 20,
                    'placeholder' => 'Masukkan NIDK',
                    'message' => $errors->first('nidk'),
                ])
            @endif
        @elseif($roleType === 'mahasiswa')
            {{-- 🔢 NIM Input (Mahasiswa) --}}
            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Nomor Induk Mahasiswa (NIM)',
                'modelString' => 'nim',
                'numberOnly' => 1,
                'maxlength' => 20,
                'placeholder' => 'Masukkan NIM',
                'message' => $errors->first('nim'),
                'isRequired' => 1,
            ])

            {{-- 📅 Tahun Angkatan Input (Mahasiswa) --}}
            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => 'Entry Year',
                'modelString' => 'tahun_angkatan',
                'typeString' => 'number',
                'placeholder' => 'Contoh: 2022',
                'message' => $errors->first('tahun_angkatan'),
                'isRequired' => 1,
            ])

        @endif