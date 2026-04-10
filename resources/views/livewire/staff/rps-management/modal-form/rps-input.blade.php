<div>
    <div
        class="px-4 py-6 mt-4
            bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Input Rencana Pembelajaran Semester</h4>

        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'rps',
            'labelString' => 'Deskripsi RPS',
            'modelString' => 'deskripsi',
            'iconString' => 'rectangle-stack',
            'placeholder' => 'Masukkan deskripsi ringkas tentang RPS...',
            'message' => $errors->first('deskripsi'),
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
            'searchString' => 'mk_search',
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
                            'labelString' => 'Kode RPS',
                            'kodeString' => 'mk_items',
                            'placeholder' => '--------',
                            'iconString' => 'clipboard-document-list',
                        ])
                    </div>
                    <div class="sm:col-span-2">
                        @include('livewire.staff.rps-management.modal-form.partial.digit-akademik')
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="grid sm:grid-cols-4 gap-1 items-end" x-data="{}" x-init="$watch('$store.rps.tahun_akademik_1', value => {
                            let year = parseInt(value);
                            if (year && year >= 0) {
                                $store.rps.tahun_akademik_2 = year + 1;
                            }
                        });
                        $watch('$store.rps.tahun_akademik_2', value => {
                            let year = parseInt(value);
                            if (year && year >= 0) {
                                $store.rps.tahun_akademik_1 = year - 1;
                            }
                        });"
                            x-effect="

                            if ($store.rps.tahun_akademik_1 && $store.rps.tahun_akademik_2) {
                                {{-- if ($store.rps.tahun_akademik_1.length < 4 || $store.rps.tahun_akademik_2.length < 4) {
                                    $store.rps.tahun_akademik = '';
                                } else { --}}
                                    $store.rps.tahun_akademik = $store.rps.tahun_akademik_1 + '/' + $store.rps.tahun_akademik_2;
                                {{-- } --}}
                            } else {
                                $store.rps.tahun_akademik = '';
                            }
                        ">

                            <div class="sm:col-span-2">
                                @include('livewire.global.modal-form.input-form', [
                                    'alpine' => 'rps',
                                    'labelString' => 'Tahun Akademik',
                                    'modelString' => 'tahun_akademik_1',
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
                                    'labelString' => 'Tahun Akademik',
                                    'modelString' => 'tahun_akademik_2',
                                    'numberOnly' => 1,
                                    'maxlength' => 4,
                                    'iconString' => 'calendar-days',
                                    'placeholder' => 'Contoh: 2026',
                                    'isFocusSelect' => 1,
                                    'noLabel' => 1,
                                ])
                            </div>
                        </div>

                        @error('tahun_akademik')
                            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('tahun_akademik') }}</span>
                        @enderror
                    </div>

                </div>

            </div>
        </div>


        <div x-data="{}">
            {{-- Kondisi 1: Kurang dari 14 (Hanya Draf) --}}
            <template x-if="$store.rps.count_scpmk < 14">
                <div wire:key="status-draf-only">
                    @include('livewire.global.modal-form.select-form', [
                        'alpine' => 'rps',
                        'labelString' => 'Draf / Aktif',
                        'modelString' => 'is_draf',
                        'xOptions' => ['Draf'],
                        'xValues' => [1],
                        'iconString' => 'tag',
                        'placeholder' => 'Status Aktif Terkunci...',
                        'message' => $errors->first('is_draf'),
                    ])
                    <p class="mt-2 text-sm text-red-500 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="information-circle" variant="mini" class="w-3 h-3" />
                        Status "Aktif" terkunci. Sub-CPMK baru <span x-text="$store.rps.count_scpmk + '/14'"></span>
                    </p>
                </div>
            </template>

            <template x-if="$store.rps.count_scpmk >= 14">
                <div wire:key="status-full-options">
                    @include('livewire.global.modal-form.select-form', [
                        'alpine' => 'rps',
                        'labelString' => 'Draf / Aktif',
                        'modelString' => 'is_draf',
                        'xOptions' => ['Draf', 'Aktif'],
                        'xValues' => [1, 0],
                        'iconString' => 'tag',
                        'placeholder' => 'Pilih Status...',
                        'message' => $errors->first('is_draf'),
                    ])
                    <p class="mt-2 text-sm text-emerald-600 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="check-circle" variant="mini" class="w-3 h-3" />
                        Syarat minimal pertemuan terpenuhi <span x-text="'(' + $store.rps.count_scpmk + '/14)'"></span>
                    </p>
                </div>
            </template>
        </div>

    </div>

    @include('livewire.staff.rps-management.modal-form.rps-input-partial.rps-cpmk-input')
    @include('livewire.staff.rps-management.modal-form.rps-input-partial.rps-cpl-input')
    @include('livewire.staff.rps-management.modal-form.rps-input-partial.rps-referensi-input')
    @include('livewire.staff.rps-management.modal-form.rps-input-partial.rps-dosen-input')

    <div x-data x-init="$watch('$store.rps.mk_id', value => console.log('mk_id: ', value))"></div>
    <div x-data x-init="$watch('$store.rps.mk_items', value => console.log('mk_items: ', value))"></div>

</div>
