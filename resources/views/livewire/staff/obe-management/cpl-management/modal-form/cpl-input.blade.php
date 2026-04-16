<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Capaian Pembelajaran Lulusan</h4>


        <div>
            <div class="grid sm:grid-cols-4 gap-3 items-end" x-data="{}"
                x-effect="$store.cpl.kode_cpl = ($store.cpl.kode_cpl_1 || '') + ($store.cpl.kode_cpl_2 || '')">

                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'cpl',
                        'nameXString' => 'Kode CPL',
                        'modelString' => 'kode_cpl_1',
                        'iconString' => 'document-text',
                        'placeholder' => 'Masukkan huruf Kode CPL...',
                        'isKode' => 4,
                        'isFocusSelect' => 1,
                    ])
                </div>
                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'cpl',
                        'noLabel' => 1,
                        'modelString' => 'kode_cpl_2',
                        'numberOnly' => 1,
                        'maxlength' => 6,
                        'iconString' => 'variable',
                        'placeholder' => 'Contoh: 121104',
                        'isFocusSelect' => 1,
                    ])
                </div>
            </div>
            @error('kode_cpl')
                <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_cpl') }}</span>
            @enderror
        </div>


        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'cpl',
            'nameXString' => 'Deskripsi',
            'modelString' => 'deskripsi',
            'iconString' => 'document-text',
            'placeholder' => 'Masukkan deskripsi CPL...',
            'message' => $errors->first('deskripsi'),
        ])
    </div>

    <template x-if="$store.cpl?.isEdit == 1">
        @include('livewire.staff.obe-management.partial.rps-list', [
            'rps_items_list' => $cpl_rps_items_list,
            'rps_modal_paginator' => $cpl_rps_modal_paginator,
            'nameXString' => 'CPL',
        ])
    </template>

</div>
