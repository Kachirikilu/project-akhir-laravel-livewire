<div x-data="{ activeTab: @entangle('switchTable'), activeFilter: @entangle('filterRPS') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">



    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-50">

        {{-- BAGIAN FILTER ATAS --}}
        <div x-show="activeTab == 'rps'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'RPS',
                'filterByFunc' => 'filterByRPS',
                'filterString' => 'filterRPS',
                'totalTab' => $totalRPS,
                'totalTab1' => $stats['rps-akademik'],
                'totalTab2' => $stats['rps-ref-new'],
                'totalTab3' => $stats['rps-aktif'],
                'totalTab4' => $stats['rps-draf'],
                'totalTab5' => $stats['rps-5-years'],
                'tab1String' => 'rps-akademik',
                'tab2String' => 'rps-ref-new',
                'tab3String' => 'rps-aktif',
                'tab4String' => 'rps-draf',
                'tab5String' => 'rps-5-years',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Direvisi Baru',
                'tab3Name' => 'Aktif',
                'tab4Name' => 'Draf',
                'tab5Name' => '5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                'key' => 'page-control-rps'
            ])
        </div>

        <div x-show="activeTab == 'cpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'CPMK',
                'filterByFunc' => 'filterByCPMK',
                'filterString' => 'filterCPMK',
                'totalTab' => $totalCPMK,
                'totalTab1' => $stats['cpmk-month'],
                'totalTab2' => $stats['cpmk-6-months'],
                'totalTab3' => $stats['cpmk-year'],
                'totalTab4' => $stats['cpmk-5-years'],
                'tab1String' => 'cpmk-month',
                'tab2String' => 'cpmk-6-months',
                'tab3String' => 'cpmk-year',
                'tab4String' => 'cpmk-5-years',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Semester Ini',
                'tab3Name' => 'Tahun Ini',
                'tab4Name' => '5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300],
                'key' => 'page-control-cpmk'
            ])
        </div>

        <div x-show="activeTab == 'scpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
            'typeXString' => 'SCPMK',
            'filterByFunc' => 'filterBySCPMK',
            'filterString' => 'filterSCPMK',
            'totalTab' => $totalSCPMK,
            'totalTab1' => $stats['scpmk-month'],
            'totalTab2' => $stats['scpmk-6-months'],
            'totalTab3' => $stats['scpmk-year'],
            'totalTab4' => $stats['scpmk-5-years'],
            'tab1String' => 'scpmk-month',
            'tab2String' => 'scpmk-6-months',
            'tab3String' => 'scpmk-year',
            'tab4String' => 'scpmk-5-years',
            'tab1Name' => 'Terbaru',
            'tab2Name' => 'Semester Ini',
            'tab3Name' => 'Tahun Ini',
            'tab4Name' => '5 Tahun Lalu'
        ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300, 500],
                'key' => 'page-control-scpmk'
            ])
        </div>


        <div x-show="activeTab == 'cpl'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'CPL',
                'filterByFunc' => 'filterByCPL',
                'filterString' => 'filterCPL',
                'totalTab' => $totalCPL,
                'totalTab1' => $stats['cpl-month'],
                'totalTab2' => $stats['cpl-6-months'],
                'totalTab3' => $stats['cpl-year'],
                'totalTab4' => $stats['cpl-5-years'],
                'tab1String' => 'cpl-month',
                'tab2String' => 'cpl-6-months',
                'tab3String' => 'cpl-year',
                'tab4String' => 'cpl-5-years',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Semester Ini',
                'tab3Name' => 'Tahun Ini',
                'tab4Name' => '5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100],
                'key' => 'page-control-cpl'
            ])
        </div>

        <div x-show="activeTab == 'ref'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'Referensi',
                'filterByFunc' => 'filterByRef',
                'filterString' => 'filterRef',
                'totalTab' => $totalRef,
                'totalTab1' => $stats['ref-year'],
                'totalTab2' => $stats['ref-3-years'],
                'totalTab3' => $stats['ref-5-years'],
                'totalTab4' => $stats['ref-10-years'],
                'tab1String' => 'ref-month',
                'tab2String' => 'ref-6-months',
                'tab3String' => 'ref-5-years',
                'tab4String' => 'ref-10-years',
                'tab1Name' => 'Terbaru',
                'tab2Name' => '3 Tahun Lalu',
                'tab3Name' => '5 Tahun Lalu',
                'tab4Name' => '10 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                'key' => 'page-control-ref'
            ])
        </div>

    </div>



    {{-- BAGIAN SEARCH UTAMA --}}
    <div {{-- x-show="activeFilter !== 'universitas'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" --}}
                class="grid grid-cols-1 sm:grid-cols-7 gap-3 z-20">

        {{-- Tab RPS --}}
        <div class="sm:col-span-4">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL, dan Referensi...',
            ])
        </div>

        {{-- Tab CPMK --}}
        {{-- <div x-show="activeTab === 'cpmk'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 sm:col-span-4">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Capaian Pembelajaran Mata Kuliah...',
                ])
            </div> --}}

        {{-- Tab Sub-CPMK --}}
        {{-- <div x-show="activeTab === 'scpmk'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 sm:col-span-4">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sub Capaian Pembelajaran Mata Kuliah...',
                ])
            </div> --}}

        {{-- Tab CPL --}}
        {{-- <div x-show="activeTab === 'cpl'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 sm:col-span-4">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Capaian Pembelajaran Lulusan...',
                ])
            </div> --}}

        {{-- Tab Referensi --}}
        {{-- <div x-show="activeTab === 'ref'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 sm:col-span-4">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Referensi...',
                ])
            </div> --}}

        <div class="order-3 sm:order-2 sm:col-span-3 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputProdiFilter',
                'xSearchResultsString' => 'prodiSearchResults',
                'selectedXNameString' => 'prodi_name',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan Program Studi...',
                'xSearchQueryString' => 'prodiSearchQuery',
                'selectedXId' => $selectedProdiId,
                'selectedXName' => $prodi_name,
                'resetXFilter' => 'resetProdiFilter()',
                'xSearchQuery' => $prodiSearchQuery,
                'xSearchResults' => $prodiSearchResults,
                'selectXForFilterString' => 'selectProdiForFilter',
                'typeXString' => 'prodi',
                'unfoundString' => 'Tidak ada Program Studi ditemukan!',
            ])
        </div>
    </div>


    {{-- BAGIAN SECONDARY SEARCH --}}
    <div {{-- x-show="activeFilter !== 'universitas'" x-transition:enter="transition ease-out duration-600"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4" --}} class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputJurusanFilter',
                'xSearchResultsString' => 'jurusanSearchResults',
                'selectedXNameString' => 'jurusan_name',
                'iconString' => 'book-open',
                'placeholderString' => 'Filter berdasarkan Jurusan...',
                'xSearchQueryString' => 'jurusanSearchQuery',
                'selectedXId' => $selectedJurusanId,
                'selectedXName' => $jurusan_name,
                'resetXFilter' => 'resetJurusanFilter()',
                'xSearchQuery' => $jurusanSearchQuery,
                'xSearchResults' => $jurusanSearchResults,
                'selectXForFilterString' => 'selectJurusanForFilter',
                'typeXString' => 'jurusan',
                'unfoundString' => 'Tidak ada Jurusan ditemukan!',
            ])
        </div>

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputFakultasFilter',
                'xSearchResultsString' => 'fakultasSearchResults',
                'selectedXNameString' => 'fakultas_name',
                'iconString' => 'building-library',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fakultasSearchQuery',
                'selectedXId' => $selectedFakultasId,
                'selectedXName' => $fakultas_name,
                'resetXFilter' => 'resetFakultasFilter()',
                'xSearchQuery' => $fakultasSearchQuery,
                'xSearchResults' => $fakultasSearchResults,
                'selectXForFilterString' => 'selectFakultasForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputMatkulFilter',
                'xSearchResultsString' => 'matkulSearchResults',
                'selectedXNameString' => 'matkul_name',
                'iconString' => 'book-open',
                'placeholderString' => 'Filter berdasarkan Mata Kuliah...',
                'xSearchQueryString' => 'matkulSearchQuery',
                'selectedXId' => $selectedMatkulId,
                'selectedXName' => $matkul_name,
                'resetXFilter' => 'resetMatkulFilter()',
                'xSearchQuery' => $matkulSearchQuery,
                'xSearchResults' => $matkulSearchResults,
                'selectXForFilterString' => 'selectMatkulForFilter',
                'typeXString' => 'matkul',
                'unfoundString' => 'Tidak ada Mata Kuliah ditemukan!',
            ])
        </div>

        {{-- <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputFakultasFilter',
                'xSearchResultsString' => 'fakultasSearchResults',
                'selectedXNameString' => 'fakultas_name',
                'iconString' => 'building-library',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fakultasSearchQuery',
                'selectedXId' => $selectedFakultasId,
                'selectedXName' => $fakultas_name,
                'resetXFilter' => 'resetFakultasFilter()',
                'xSearchQuery' => $fakultasSearchQuery,
                'xSearchResults' => $fakultasSearchResults,
                'selectXForFilterString' => 'selectFakultasForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div> --}}
    </div>
</div>
