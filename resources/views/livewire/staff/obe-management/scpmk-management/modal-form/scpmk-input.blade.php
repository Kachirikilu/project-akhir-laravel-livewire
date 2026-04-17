<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Sub Capaian Pembelajaran Mata Kuliah</h4>


        <div>
            <div class="grid sm:grid-cols-4 gap-3 items-end" x-data="{}"
                x-effect="$store.scpmk.kode_scpmk = ($store.scpmk.kode_scpmk_1 || '') + ($store.scpmk.kode_scpmk_2 || '')">

                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'scpmk',
                        'nameXString' => 'Kode Sub-CPMK',
                        'modelString' => 'kode_scpmk_1',
                        'iconString' => 'academic-cap',
                        'placeholder' => 'Masukkan huruf Kode Sub-CPMK...',
                        'isKode' => 4,
                        'isFocusSelect' => 1,
                    ])
                </div>
                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'scpmk',
                        'noLabel' => 1,
                        'modelString' => 'kode_scpmk_2',
                        'numberOnly' => 1,
                        'maxlength' => 6,
                        'iconString' => 'variable',
                        'placeholder' => 'Contoh: 121104',
                        'isFocusSelect' => 1,
                    ])
                </div>
            </div>
            @error('kode_scpmk')
                <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_scpmk') }}</span>
            @enderror
        </div>

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'scpmk',
            'nameXString' => 'Deskripsi',
            'modelString' => 'deskripsi',
            'iconString' => 'document-text',
            'placeholder' => 'Masukkan deskripsi ringkas tentang Sub-CPMK...',
            'message' => $errors->first('deskripsi'),
        ])

    </div>

    @include('livewire.staff.obe-management.scpmk-management.modal-form.scpmk-partial.scpmk-materi-input')
    @include('livewire.staff.obe-management.scpmk-management.modal-form.scpmk-partial.scpmk-metode-input')
    @include('livewire.staff.obe-management.scpmk-management.modal-form.scpmk-partial.scpmk-ref-input')

    <template x-if="$store.scpmk?.isEdit == 1">
        @include('livewire.staff.obe-management.obe-partial.rps-list', [
            'rps_items_list' => $scpmk_rps_items_list,
            'rps_modal_paginator' => $scpmk_rps_modal_paginator,
            'nameXString' => 'Sub-CPMK',
            'wireLoading' => 'SCPMK'
        ])
    </template>

    {{-- <div x-data x-init="$watch('$store.scpmk.kode_scpmk', value => console.log('kode_scpmk: ', value))"></div>
    <div x-data x-init="$watch('$store.scpmk.kode_scpmk_1', value => console.log('kode_scpmk_1: ', value))"></div>
    <div x-data x-init="$watch('$store.scpmk.kode_scpmk_2', value => console.log('kode_scpmk_2: ', value))"></div>
    <div x-data x-init="$watch('$store.scpmk.deskripsi', value => console.log('deskripsi: ', value))"></div> --}}
    {{-- <div x-data x-init="$watch('$store.scpmk.mk_items', value => console.log('mk_items: ', value))"></div> --}}

</div>
