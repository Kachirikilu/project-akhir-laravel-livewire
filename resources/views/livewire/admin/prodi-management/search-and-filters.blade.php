<div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">

    <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        @if ($this->switchTable == 'prodi')
            @include('livewire.admin.global.search-and-filters.filter-mode', [
                'typeOfXString' => 'strata',
                'totalTab' => $totalProdis,
                'totalTab1' => $totalSarjanas,
                'totalTab2' => $totalMagisters,
                'totalTab3' => $totalDoktors,
                'tab1String' => 'sarjana',
                'tab2String' => 'magister',
                'tab3String' => 'doktor',
            ])
        @else
            <div></div>
        @endif

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.admin.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25],
        ])

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-center w-full">
        @if ($switchTable !== 'fakultas')
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' =>
                    $switchTable == 'prodi'
                        ? 'Cari Prodi, Strata, atau Jurusan...'
                        : 'Cari Jurusan, Fakultas, atau ID Jurusan...',
            ])
        @else
            <div class="sm:col-span-4 relative"></div>
        @endif

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
            'typeOfXString' => 'fakultas',
            'unfoundString' => 'Tidak ada fakultas ditemukan!',
        ])

    </div>
</div>
