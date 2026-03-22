<div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">

    @if ($this->switchTable == 'prodi')
        <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

            {{-- Bagian Tab / Link (Kiri) --}}
            @include('livewire.admin.global.search-and-filters.filter-mode', [
                'typeXString' => 'strata',
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
    @endif


    @if ($switchTable == 'prodi')
        <div class="order-2 sm:order-1 sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' =>
                    $switchTable == 'prodi'
                        ? 'Cari Prodi, Strata, atau Jurusan...'
                        : 'Cari Jurusan, Fakultas, atau ID Jurusan...',
            ])
        </div>
    @endif


    <div class="grid grid-cols-1 sm:grid-cols-{{ $switchTable == 'prodi' ? '8 mt-2' : '9' }} gap-2 items-center w-full">
        @if ($switchTable !== 'fakultas')
            <div class="order-2 sm:order-1 sm:col-span-4 relative">
                @include('livewire.admin.global.search-and-filters.secondary-search', [
                    'selectedXNameString' => 'selectedJurusanName',
                    'iconString' => 'book-open',
                    'placeholderString' => 'Filter berdasarkan Jurusan...',
                    'xSearchQueryString' => 'jurusanSearchQuery',
                    'selectedXId' => $selectedJurusanId,
                    'selectedXName' => $selectedJurusanName,
                    'resetXFilterString' => 'resetJurusanFilter',
                    'xSearchQuery' => $jurusanSearchQuery,
                    'xSearchResults' => $jurusanSearchResults,
                    'selectXForFilterString' => 'selectJurusanForFilter',
                    'typeXString' => 'jurusan',
                    'unfoundString' => 'Tidak ada jurusan ditemukan!',
                ])
            </div>
        @else
          <div class="sm:col-span-4 hidden sm:block"></div>
        @endif

        <div class="order-3 sm:order-2 sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.secondary-search', [
                'selectedXNameString' => 'selectedFakultasName',
                'iconString' => 'building-office',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fakultasSearchQuery',
                'selectedXId' => $selectedFakultasId,
                'selectedXName' => $selectedFakultasName,
                'resetXFilterString' => 'resetFakultasFilter',
                'xSearchQuery' => $fakultasSearchQuery,
                'xSearchResults' => $fakultasSearchResults,
                'selectXForFilterString' => 'selectFakultasForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada fakultas ditemukan!',
            ])
        </div>

        @if ($switchTable !== 'prodi')
            <div class="order-1 sm:order-3 sm:col-span-1">
                @include('livewire.admin.global.search-and-filters.page-control', [
                    'perPageOptions' => match ($switchTable) {
                        'jurusan' => [3, 5, 8, 10, 15, 25, 50],
                        'fakultas' => [3, 5, 8, 10],
                    },
                    'withFull' => 0,
                ])
            </div>
        @endif
    </div>

</div>
