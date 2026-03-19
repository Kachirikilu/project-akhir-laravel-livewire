<div>
    {{-- ****************************************************** --}}
    {{-- 3. INPUT FAKULTAS --}}
    {{-- ****************************************************** --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Input Fakultas</h4>

        {{-- 📧 Fakultas Input --}}
        @include('livewire.admin.global.modal-form.input-form', [
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
        @include('livewire.admin.global.modal-form.input-form', [
            'labelString' => 'Kode Fakultas',
            'modelString' => 'kode_fk',
            'iconString' => 'hashtag',
            'placeholder' => 'Masukkan 3 huruf Kode Fakultas',
            'message' => $errors->first('kode_fk'),
            'isKode' => 3,
            'isRequired' => 1
        ])
    </div>
</div>

