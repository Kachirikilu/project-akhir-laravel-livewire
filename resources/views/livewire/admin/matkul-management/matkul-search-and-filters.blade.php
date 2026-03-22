<div x-data="{ activeFilter: @entangle('filter') }" class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    {{-- BAGIAN FILTER ATAS (Hanya untuk Prodi) --}}
    <div x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.admin.global.search-and-filters.filter-mode', [
            'typeXString' => 'Opsi',
            'totalTab' => $totalMatkuls,
            'totalTab1' => $totalWajib,
            'totalTab2' => $totalPilihan,
            'totalTab3' => $totalUni,
            'tab1String' => 'wajib',
            'tab2String' => 'pilihan',
            'tab3String' => 'universal',
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.admin.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
        ])
    </div>

    {{-- BAGIAN SEARCH UTAMA --}}
    <div x-show="activeFilter !== 'universal'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Mata Kuliah...',
            ])
        </div>

        <div class="order-3 sm:order-2 sm:col-span-3 relative">
            @include('livewire.admin.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputProdiFilter',
                'xSearchResultsString' => 'prodiSearchResults',
                'selectedXNameString' => 'selectedProdiName',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan Program Studi...',
                'xSearchQueryString' => 'prodiSearchQuery',
                'selectedXId' => $selectedProdiId,
                'selectedXName' => $selectedProdiName,
                'resetXFilter' => 'resetProdiFilter()',
                'xSearchQuery' => $prodiSearchQuery,
                'xSearchResults' => $prodiSearchResults,
                'selectXForFilterString' => 'selectProdiForFilter',
                'typeXString' => 'prodi',
                'unfoundString' => 'Tidak ada Program Studi ditemukan!',
            ])
        </div>
    </div>

    <div x-show="activeFilter == 'universal'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="grid grid-cols-1 gap-2 items-center w-full sm:grid-cols-9'">

        {{-- Parent Wrapper --}}
        <div class="grid order-2 sm:order-1 sm:col-span-8 relative">
            {{-- Tab Prodi --}}
            <div x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1">
                @include('livewire.admin.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mata Kuliah...',
                ])
            </div>
        </div>


    </div>

    {{-- BAGIAN SECONDARY SEARCH (Jurusan, & Fakultas) --}}
    <div x-show="activeFilter !== 'universal'" x-transition:enter="transition ease-out duration-600"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputJurusanFilter',
                'xSearchResultsString' => 'jurusanSearchResults',
                'selectedXNameString' => 'selectedJurusanName',
                'iconString' => 'book-open',
                'placeholderString' => 'Filter berdasarkan Jurusan...',
                'xSearchQueryString' => 'jurusanSearchQuery',
                'selectedXId' => $selectedJurusanId,
                'selectedXName' => $selectedJurusanName,
                'resetXFilter' => 'resetJurusanFilter()',
                'xSearchQuery' => $jurusanSearchQuery,
                'xSearchResults' => $jurusanSearchResults,
                'selectXForFilterString' => 'selectJurusanForFilter',
                'typeXString' => 'jurusan',
                'unfoundString' => 'Tidak ada Jurusan ditemukan!',
            ])
        </div>

        <div class="sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputFakultasFilter',
                'xSearchResultsString' => 'fakultasSearchResults',
                'selectedXNameString' => 'selectedFakultasName',
                'iconString' => 'building-library',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fakultasSearchQuery',
                'selectedXId' => $selectedFakultasId,
                'selectedXName' => $selectedFakultasName,
                'resetXFilter' => 'resetFakultasFilter()',
                'xSearchQuery' => $fakultasSearchQuery,
                'xSearchResults' => $fakultasSearchResults,
                'selectXForFilterString' => 'selectFakultasForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div>
    </div>
</div>
