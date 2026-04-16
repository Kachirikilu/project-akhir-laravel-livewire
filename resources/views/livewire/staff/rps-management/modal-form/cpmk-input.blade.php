<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Capaian Pembelajaran Mata Kuliah</h4>


        <div>
            <div class="grid sm:grid-cols-4 gap-3 items-end" x-data="{}"
                x-effect="$store.cpmk.kode_cpmk = ($store.cpmk.kode_cpmk_1 || '') + ($store.cpmk.kode_cpmk_2 || '')">

                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'cpmk',
                        'nameXString' => 'Kode CPMK',
                        'modelString' => 'kode_cpmk_1',
                        'iconString' => 'academic-cap',
                        'placeholder' => 'Masukkan huruf Kode CPMK...',
                        'isKode' => 4,
                        'isFocusSelect' => 1,
                    ])
                </div>
                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'cpmk',
                        'noLabel' => 1,
                        'modelString' => 'kode_cpmk_2',
                        'numberOnly' => 1,
                        'maxlength' => 6,
                        'iconString' => 'variable',
                        'placeholder' => 'Contoh: 121104',
                        'isFocusSelect' => 1,
                    ])
                </div>
            </div>
            @error('kode_cpmk')
                <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_cpmk') }}</span>
            @enderror
        </div>

    </div>

    @include('livewire.staff.rps-management.modal-form.cpmk-partial.cpmk-cpl-input')
    @include('livewire.staff.rps-management.modal-form.cpmk-partial.cpmk-scpmk-input')
    @include('livewire.staff.rps-management.modal-form.cpmk-partial.cpmk-ref-input')

    <template x-if="$store.cpmk?.isEdit == 1">
        @include('livewire.staff.rps-management.modal-form.partial.rps-list', [
            'rps_items_list' => $cpmk_rps_items_list,
            'rps_modal_paginator' => $cpmk_rps_modal_paginator,
            'nameXString' => 'CPMK',
        ])
    </template>

</div>
