<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Rencana Pembelajaran Semester</h4>

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'rps',
            'nameXString' => 'Deskripsi RPS',
            'modelString' => 'deskripsi',
            'iconString' => 'clipboard-document-list',
            'placeholder' => 'Masukkan deskripsi ringkas tentang RPS...',
            'message' => $errors->first('deskripsi'),
            'isRequired' => 0,
        ])

        @include('livewire.global.modal-form.search-input-form', [
            'alpine' => 'rps',
            'xResults' => $mkResults,
            'selectX' => 'selectMK',
            'modelString' => 'nama_mk_search',
        
            'idString' => 'mk_id',
            'itemsAllString' => 'mk_items',
        
            'resetXInput' => 'resetMKInput()',
            'typeXString' => 'mk',
            // 'typeX2String' => 'jurusan',
            // 'typeX3String' => 'fakultas',
        
            'nameXString' => 'Mata Kuliah',
            'nameSearchString' => 'mkNameSearch',
            'fetchString' => 'fetchMK',
            'iconString' => 'rectangle-stack',
            'wireLoading' => 'fetchMK',
        ])

        <div class="relative">

            <div class="space-y-4">

                <div class="grid sm:grid-cols-6 gap-1 items-end">
                    <div class="sm:col-span-4">

                        @include('livewire.global.modal-form.kode-input', [
                            'alpine' => 'rps',
                            'nameXString' => 'Kode RPS',
                            'kodeString' => 'mk_items',
                            'placeholder' => '--------',
                            'iconString' => 'clipboard-document-list',
                        ])
                    </div>
                    <div class="sm:col-span-2">
                        @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-digit-akademik')
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="grid sm:grid-cols-4 gap-1 items-end" x-data="{}" x-init="$watch('$store.rps.akademik_1', value => {
                            let year = parseInt(value);
                            if (year && year >= 0) {
                                $store.rps.akademik_2 = year + 1;
                            }
                            });
                            $watch('$store.rps.akademik_2', value => {
                                let year = parseInt(value);
                                if (year && year >= 0) {
                                    $store.rps.akademik_1 = year - 1;
                                }
                            });"
                            x-effect="
                            if ($store.rps.akademik_1 && $store.rps.akademik_2) {
                                $store.rps.akademik = $store.rps.akademik_1 + '/' + $store.rps.akademik_2;
                            } else {
                                $store.rps.akademik = '';
                            }
                        ">

                            <div class="sm:col-span-2">
                                @include('livewire.global.modal-form.input-form', [
                                    'alpine' => 'rps',
                                    'nameXString' => 'Tahun Akademik',
                                    'modelString' => 'akademik_1',
                                    'numberOnly' => 1,
                                    'maxlength' => 4,
                                    'iconString' => 'calendar-days',
                                    'placeholder' => 'Contoh: 2025',
                                    'isFocusSelect' => 1,
                                ])
                            </div>
                            <div class="sm:col-span-2">
                                {{-- @include('livewire.staff.rps-management.modal-form.partial.tahun-akademik-2') --}}
                                @include('livewire.global.modal-form.input-form', [
                                    'alpine' => 'rps',
                                    'nameXString' => 'Tahun Akademik',
                                    'modelString' => 'akademik_2',
                                    'numberOnly' => 1,
                                    'maxlength' => 4,
                                    'iconString' => 'calendar-days',
                                    'placeholder' => 'Contoh: 2026',
                                    'isFocusSelect' => 1,
                                    'noLabel' => 1,
                                ])
                            </div>
                        </div>

                        @error('akademik')
                            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('akademik') }}</span>
                        @enderror
                    </div>

                </div>

            </div>
        </div>

        @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-draf-input')

    </div>

    @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-cpmk-input')
    @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-cpl-input')
    @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-ref-input')
    @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-dosen-input')

    {{-- <div x-data x-init="$watch('$store.rps.mk_id', value => console.log('mk_id: ', value))"></div>
    <div x-data x-init="$watch('$store.rps.mk_items', value => console.log('mk_items: ', value))"></div> --}}

</div>
