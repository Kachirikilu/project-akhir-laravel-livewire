<div x-data="{ activeTab: @entangle('switchTable') }"
    x-effect="activeTab; open = false"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    {{-- BAGIAN FILTER ATAS --}}
    <div x-show="activeTab === 'prodi'" x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="relative z-40 isolate border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.global.search-and-filters.filter-mode', [
            'typeXString' => 'Strata',
            'filterByFunc' => 'filterByStrata',
            'filterString' => 'filterPr',
            'totalTab' => $totalProdis,
            'totalTab1' => $totalSarjanas,
            'totalTab2' => $totalMagisters,
            'totalTab3' => $totalDoktors,
            'tab1String' => 'sarjana',
            'tab2String' => 'magister',
            'tab3String' => 'doktor',
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75],
            'key' => 'page-control-prodi'
        ])
    </div>

    {{-- BAGIAN SEARCH UTAMA --}}

    <div class="grid grid-cols-1 grid-rows-1 gap-2 items-center w-full z-20">
        <div x-show="activeTab === 'prodi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="col-start-1 row-start-1 relative w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Program Studi, Jurusan, atau Fakultas...',
            ])
        </div>

        <div x-show="activeTab !== 'prodi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="relative z-50 isolate col-start-1 row-start-1 grid grid-cols-1 grid-rows-1 relative w-full">

                        {{-- Tab Jurusan --}}
            <div x-show="activeTab === 'jurusan'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 grid grid-cols-1 sm:grid-cols-9 gap-2 items-center">
                <div class="col-start-1 row-start-1 sm:col-span-8">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Jurusan atau relasinya...',
                    ])
                </div>
                <div class="col-start-2 row-start-1 sm:col-span-1">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [3, 5, 8, 10, 15, 25, 50],
                        'key' => 'page-control-jurusan',
                        'withFull' => 0,
                    ])
                </div>
            </div>

            {{-- Tab Fakultas --}}
            <div x-show="activeTab === 'fakultas'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 grid grid-cols-1 sm:grid-cols-9 gap-2 items-center">
                <div class="col-start-1 row-start-1 sm:col-span-8">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Fakultas atau relasinya...',
                ])
                </div>
                <div class="col-start-1 row-start-1 sm:col-span-1">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10],
                    'key' => 'page-control-fakultas',
                    'withFull' => 0,
                ])
                </div>
            </div>
        
        </div>


    </div>

    {{-- BAGIAN SECONDARY SEARCH (Jurusan & Fakultas) --}}
    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full z-10">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputJrFilter',
                'xSearchResultsString' => 'jrSearchResults',
                'selectedXNameString' => 'jr_name',
                'iconString' => 'book-open',
                'placeholderString' => 'Filter berdasarkan Jurusan...',
                'xSearchQueryString' => 'jrSearchQuery',
                'selectedXId' => $selectedJrId,
                'selectedXName' => $jr_name,
                'resetXFilter' => 'resetJrFilter()',
                'xSearchQuery' => $jrSearchQuery,
                'xSearchResults' => $jrSearchResults,
                'selectXForFilterString' => 'selectJrForFilter',
                'typeXString' => 'jurusan',
                'unfoundString' => 'Tidak ada Jurusan ditemukan!',
            ])
        </div>

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputFkFilter',
                'xSearchResultsString' => 'fkSearchResults',
                'selectedXNameString' => 'fk_name',
                'iconString' => 'building-library',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fkSearchQuery',
                'selectedXId' => $selectedFkId,
                'selectedXName' => $fk_name,
                'resetXFilter' => 'resetFkFilter()',
                'xSearchQuery' => $fkSearchQuery,
                'xSearchResults' => $fkSearchResults,
                'selectXForFilterString' => 'selectFkForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div>
    </div>
</div>
