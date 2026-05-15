<div
    class="px-4 py-6 mt-4 
        bg-[var(--main-table-color)] border-[var(--border-table-color)]
        shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Kelas Perkuliahan</h4>

    <div>
        <div class="grid sm:grid-cols-4 gap-1 sm:gap-3 items-end" x-data="{}"
            x-effect="if($store.jadwal) $store.jadwal.kode_jadwal = ($store.jadwal.kode_jadwal_1 || '') + ($store.jadwal.kode_jadwal_2 || '')">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Kode Kelas',
                    'modelString' => 'kode_jadwal_1',
                    'iconString' => 'document-text',
                    'placeholder' => 'Masukkan huruf Kode Kelas...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'noLabel' => 1,
                    'modelString' => 'kode_jadwal_2',
                    'numberOnly' => 1,
                    'maxlength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 121104',
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>
        @error('kode_jadwal')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_jadwal') }}</span>
        @enderror
    </div>

    {{-- 📧 Mata Kuliah Input --}}
    {{-- @include('livewire.global.modal-form.input-form', [
        'alpine' => 'jadwal',
        'nameXString' => 'Nama Kelas',
        'modelString' => 'nama_jadwal',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan nama Kelas...',
        'message' => $errors->first('nama_jadwal'),
    ]) --}}

    {{-- @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'jadwal',
        'nameXString' => 'Deskripsi Kelas',
        'modelString' => 'deskripsi',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Deskripsi dari Kelas...',
        'message' => $errors->first('deskripsi'),
        'isRequired' => 0,
    ]) --}}

    <div x-data x-init="$watch('$store.jadwal?.kode_jadwal', value => console.log('kode_jadwal: ', value))"></div>
</div>
