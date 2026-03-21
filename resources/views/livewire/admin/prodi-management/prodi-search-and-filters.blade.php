<div x-data="{ activeTab: @entangle('switchTable') }" class="mb-6 p-4 bg-white dark:bg-neutral-800 rounded-lg shadow-md border border-gray-100 dark:border-neutral-700 transition-colors duration-300">

    {{-- BAGIAN FILTER ATAS (Hanya untuk Prodi) --}}
    <div x-show="activeTab === 'prodi'" x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b dark:border-neutral-700 mb-4 gap-4">
        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.admin.global.search-and-filters.filter-mode', [
            'typeOfXString' => 'Strata',
            'totalTab' => $totalProdis,
            'totalTab1' => $totalSarjanas,
            'totalTab2' => $totalMagisters,
            'totalTab3' => $totalDoktors,
            'tab1String' => 'sarjana',
            'tab2String' => 'magister',
            'tab3String' => 'doktor',
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.admin.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75],
        ])
    </div>

    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 gap-2 items-center w-full"
        :class="activeTab === 'prodi' ? 'sm:grid-cols-8' : 'sm:grid-cols-9'">

        {{-- <div class="order-2 sm:order-1 sm:col-span-8 relative">
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' => match ($switchTable) {
                    'prodi' => 'Cari Program Studi, Jurusan, atau Fakultas...',
                    'jurusan' => 'Cari Jurusan, Fakultas, atau ID Jurusan...',
                    'fakultas' => 'Cari Fakultas, atau ID Fakultas...',
                },
            ])
        </div> --}}

        {{-- Parent Wrapper --}}
        <div class="grid order-2 sm:order-1 sm:col-span-8 relative">
            {{-- Tab Prodi --}}
            <div x-show="activeTab === 'prodi'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                {{-- class="order-2 sm:order-1 sm:col-span-8 relative"> --}}
                class="col-start-1 row-start-1">
                @include('livewire.admin.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Program Studi, Jurusan, atau Fakultas...',
                ])
            </div>

            {{-- Tab Jurusan --}}
            <div x-show="activeTab === 'jurusan'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1">
                @include('livewire.admin.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Jurusan atau relasinya...',
                ])
            </div>

            {{-- Tab Fakultas --}}
            <div x-show="activeTab === 'fakultas'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1">
                @include('livewire.admin.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Fakultas atau relasinya...',
                ])
            </div>
        </div>


        {{-- Page Control untuk Jurusan/Fakultas --}}
        {{-- <div x-show="activeTab !== 'prodi'" class="order-1 sm:order-3 sm:col-span-1">
            @include('livewire.admin.global.search-and-filters.page-control', [
                'perPageOptions' => match ($switchTable) {
                    'jurusan' => [3, 5, 8, 10, 15, 25, 50],
                    'fakultas' => [3, 5, 8, 10],
                    default => [3, 5, 8, 10, 15, 25, 50, 75],
                },
                'withFull' => 0,
            ])
        </div> --}}
        <div x-show="activeTab === 'jurusan'" class="order-1 sm:order-3 sm:col-span-1">
            @include('livewire.admin.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50],
                'withFull' => 0,
            ])
        </div>
        <div x-show="activeTab === 'fakultas'" class="order-1 sm:order-3 sm:col-span-1">
            @include('livewire.admin.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10],
                'withFull' => 0,
            ])
        </div>
    </div>

    {{-- BAGIAN SECONDARY SEARCH (Jurusan & Fakultas) --}}
    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

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
                'typeOfXString' => 'jurusan',
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
                'typeOfXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div>
    </div>
</div>
