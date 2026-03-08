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

        @include('livewire.admin.user-management.modal-form.partial.input', [
            'colorIcon' => $colorIcon,
            'labelString' => match ($roleType) {
                'admin', 'dosen' => 'Nomor Induk Pegawai (NIP)',
                'mahasiswa' => 'Nomor Induk Mahasiswa (NIM)',
                default => 'Nomor Induk',
            },
            'modelString' => match ($roleType) {
                'admin', 'dosen' => 'nip',
                'mahasiswa' => 'nim',
                default => 'nomor_induk',
            },
            'numberOnly' => 1,
            'maxlength' => 20,
            'placeholder' =>
                'Masukkan ' .
                match ($roleType) {
                    'admin', 'dosen' => 'Masukkan NIP',
                    'mahasiswa' => 'Masukkan NIM',
                    default => 'Masukkan Nomor Induk',
                },
            'message' => $errors->first(
                match ($roleType) {
                    'admin', 'dosen' => 'nip',
                    'mahasiswa' => 'nim',
                    default => 'nomor_induk',
                }),
            'isRequired' => 1,
        ])

        @include('livewire.admin.user-management.modal-form.partial.input', [
            'colorIcon' => $colorIcon,
            'labelString' => match ($roleType) {
                'admin' => 'Nomor Induk Tenaga Kerja (NITK)',
                'dosen' => 'Nomor Induk Dosen Nasional (NIDN)',
                default => 'Nomor Induk Kedua',
            },
            'modelString' => match ($roleType) {
                'admin', 'dosen' => 'nitk',
                'mahasiswa' => 'nidn',
                default => 'nomor_induk_kedua',
            },
            'numberOnly' => 1,
            'maxlength' => 20,
            'placeholder' =>
                'Masukkan ' .
                match ($roleType) {
                    'admin', 'dosen' => 'Masukkan NITK',
                    'mahasiswa' => 'Masukkan NIDN',
                    default => 'Masukkan Nomor Induk Kedua',
                },
            'message' => $errors->first(
                match ($roleType) {
                    'admin', 'dosen' => 'nitk',
                    'mahasiswa' => 'nidn',
                    default => 'nomor_induk_kedua',
                }),
            'isRequired' => 0,
        ])

        @if ($roleType === 'dosen')
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


        @include('livewire.admin.user-management.modal-form.prodi-input-form')

    </div>
</div>
