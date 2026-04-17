<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Referensi</h4>


        <div>
            <div class="grid sm:grid-cols-4 gap-3 items-end" x-data="{}"
                x-effect="$store.ref.kode_ref = ($store.ref.kode_ref_1 || '') + ($store.ref.kode_ref_2 || '')">

                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'ref',
                        'nameXString' => 'Kode Referensi',
                        'modelString' => 'kode_ref_1',
                        'iconString' => 'book-open',
                        'placeholder' => 'Masukkan huruf Kode Referensi...',
                        'isKode' => 4,
                        'isFocusSelect' => 1,
                    ])
                </div>
                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'ref',
                        'noLabel' => 1,
                        'modelString' => 'kode_ref_2',
                        'numberOnly' => 1,
                        'maxlength' => 6,
                        'iconString' => 'variable',
                        'placeholder' => 'Contoh: 121104',
                        'isFocusSelect' => 1,
                    ])
                </div>
            </div>
            @error('kode_ref')
                <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_ref') }}</span>
            @enderror
        </div>

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'ref',
            'nameXString' => 'Judul Referensi',
            'modelString' => 'judul',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan Judul...',
            'message' => $errors->first('judul'),
        ])

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'ref',
            'nameXString' => 'Nama Penulis',
            'modelString' => 'penulis',
            'iconString' => 'user',
            'placeholder' => 'Contoh: Wildan Athif M.',
            'message' => $errors->first('penulis'),
        ])

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'ref',
            'nameXString' => 'Penerbit',
            'modelString' => 'penerbit',
            'iconString' => 'building-office-2',
            'placeholder' => 'Contoh: IEEE atau Universitas Sriwijaya',
            'message' => $errors->first('penerbit'),
        ])

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'ref',
            'nameXString' => 'Tahun Terbit',
            'modelString' => 'tahun',
        
            'numberOnly' => 1,
            'maxlength' => 4,
            'iconString' => 'calendar-days',
            'placeholder' => 'Masukkan Tahun (Contoh: 2022)',
            'message' => $errors->first('tahun'),
        ])

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'ref',
            'nameXString' => 'Tautan Referensi',
            'modelString' => 'link',
            'iconString' => 'link',
            'placeholder' => 'https://example.com/artikel',
            'message' => $errors->first('link'),
            'isRequired' => 0,
        ])


    </div>

    <template x-if="$store.ref?.isEdit == 1">
        @include('livewire.staff.obe-management.obe-partial.rps-list', [
            'rps_items_list' => $ref_rps_items_list,
            'rps_modal_paginator' => $ref_rps_modal_paginator,
            'nameXString' => 'Referensi',
        ])
    </template>

</div>
