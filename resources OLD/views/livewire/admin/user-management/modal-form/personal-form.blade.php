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
                'mahasiswa' => 'Nomor Induk Mahasiswa (NIM)',
                default => 'Nomor Induk Pegawai (NIP)',
            },
            'modelString' => match ($roleType) {
                'mahasiswa' => 'nim',
                default => 'nip',
            },
            'numberOnly' => 1,
            'maxlength' => 20,
            'placeholder' =>
                'Masukkan ' .
                match ($roleType) {
                    'mahasiswa' => 'Masukkan NIM',
                    default => 'Masukkan NIP',
                },
            'message' => $errors->first(
                match ($roleType) {
                    'mahasiswa' => 'nim',
                    default => 'nip',
                }),
            'isRequired' => 1,
        ])

        @if ($roleType !== 'mahasiswa')
            @include('livewire.admin.user-management.modal-form.partial.input', [
                'colorIcon' => $colorIcon,
                'labelString' => match ($roleType) {
                    'dosen' => 'Nomor Induk Dosen Nasional (NIDN)',
                    default => 'Nomor Induk Tenaga Kerja (NITK)',
                },
                'modelString' => match ($roleType) {
                    'dosen' => 'nidn',
                    default => 'nitk',
                },
                'numberOnly' => 1,
                'maxlength' => 20,
                'placeholder' =>
                    'Masukkan ' .
                    match ($roleType) {
                        'dosen' => 'Masukkan NIDN',
                        default => 'Masukkan NITK',
                    },
                'message' => $errors->first(
                    match ($roleType) {
                        'dosen' => 'nidn',
                        default => 'nitk',
                    }),
            ])
        @endif

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
