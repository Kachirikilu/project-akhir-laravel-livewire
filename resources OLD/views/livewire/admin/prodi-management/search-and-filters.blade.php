<div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">

    @if ($this->switchTable == 'prodi')
        <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

            {{-- Bagian Tab / Link (Kiri) --}}
            @include('livewire.admin.global.search-and-filters.filter-mode', [
                'typeOfXString' => 'strata',
                'totalTab' => $totalProdis,
                'totalTab1' => $totalSarjanas,
                'totalTab2' => $totalMagisters,
                'totalTab3' => $totalDoktors,
                'tab1String' => 'sarjana',
                'tab2String' => 'magister',
                'tab3String' => 'doktor'
            ])

            {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
            @include('livewire.admin.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75]
            ])

        </div>
    @endif


    {{-- @if ($switchTable == 'prodi') --}}
        <div class="grid grid-cols-1 sm:grid-cols-{{ $switchTable == 'prodi' ? '8' : '9' }} gap-2 items-center w-full">
        <div class="order-2 sm:order-1 sm:col-span-8 relative">
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' => match ($switchTable) {
                        'prodi' => 'Cari Program Studi, Jurusan, atau Fakultas...',
                        'jurusan' => 'Cari Jurusan, Fakultas, atau ID Jurusan...',
                        'fakultas' => 'Cari Fakultas, atau ID Fakultas...',
                }
            ])
        </div>
        @if ($switchTable !== 'prodi')
            <div class="order-1 sm:order-3 sm:col-span-1">
                @include('livewire.admin.global.search-and-filters.page-control', [
                    'perPageOptions' => match ($switchTable) {
                        'jurusan' => [3, 5, 8, 10, 15, 25, 50],
                        'fakultas' => [3, 5, 8, 10],
                    },
                    'withFull' => 0
                ])
            </div>
        @endif
        </div>
    {{-- @endif --}}


    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">
        {{-- @if ($switchTable !== 'fakultas') --}}
            <div class="sm:col-span-4 relative">
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
                    'typeOfXString' => 'jurusan',
                    'unfoundString' => 'Tidak ada jurusan ditemukan!'
                ])
            </div>
        {{-- @else
          <div class="sm:col-span-4 hidden sm:block"></div>
        @endif --}}

        <div class="sm:col-span-4 relative">
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
                'unfoundString' => 'Tidak ada fakultas ditemukan!'
            ])
        </div>
        
        {{-- Tombol Reset Filter All --}}
        {{-- <div class="order-3 sm:col-span-1 relative">
            <button wire:click="resetAllFilters"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition duration-150 shadow-md whitespace-nowrap">
                <i class="fas fa-sync-alt mr-1"></i> Reset
            </button>
        </div> --}}

    </div>

</div>