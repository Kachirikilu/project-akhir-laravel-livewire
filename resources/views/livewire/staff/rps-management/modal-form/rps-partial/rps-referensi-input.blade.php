<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Referensi</h4>


    <div class="relative space-y-4">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">

            {{-- 1. REFERENSI UTAMA (CPMK) --}}
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.referensi-output', [
                    'alpine' => 'rps',
                    'modelString' => 'ref_cpmk',
                    'targetString' => 'CPMK',
                    'textString' => 'Sumber Utama Mata Kuliah',
                    'colorLink' => 'blue',
                ])
            </div>

            {{-- 2. REFERENSI PENDUKUNG (Sub-CPMK) --}}
            <div class="sm:col-span-2">
                <div class="sm:col-span-2">
                    @include('livewire.global.modal-form.referensi-output', [
                        'alpine' => 'rps',
                        'modelString' => 'ref_scpmk',
                        'targetString' => 'Sub-CPMK',
                        'textString' => 'Detail Sumber per Pertemuan',
                        'colorLink' => 'emerald',
                    ])
                </div>
            </div>
        </div>

        @include('livewire.global.modal-form.search-input-array-form', [
            'alpine' => 'rps',
            'xResults' => $refResults,
            'selectX' => 'selectRefArray',
            'modelString' => 'nama_ref_search',
        
            'idString' => 'ref_id_array',
            'itemsAllString' => 'ref_items_array',
        
            'typeXString' => 'judul',
            'typeX2String' => 'penulis_tahun',
            'typeX3String' => 'penerbit',
            'typeLinkString' => 'link',
        
            'nameXString' => 'Referensi',
            'nameX2String' => 'Tambah Referensi Baru',
            'nameSearchString' => 'refNameSearch',
            'fetchString' => 'fetchRef',
            'iconString' => 'book-open',
        
            'parentIdString' => 'cpmk_id_array',
            'nameXParent' => 'CPMK',
            'wireLoading' => 'fetchRef',
        
            'isRequired' => 0,
        ])

    </div>

</div>
