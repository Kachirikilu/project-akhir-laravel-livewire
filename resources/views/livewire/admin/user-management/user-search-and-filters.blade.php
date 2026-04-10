<div class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">
    <div class="border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.global.search-and-filters.filter-mode', [
            'typeXString' => 'Pengguna',
            'filterByFunc' => 'filterByUser',
            'filterString' => 'filterUser',
            'totalTab' => $totalUsers,
            'totalTab1' => $totalAdmins,
            'totalTab2' => $totalDosens,
            'totalTab3' => $totalMahasiswas,
            'tab1String' => 'admin',
            'tab2String' => 'dosen',
            'tab3String' => 'mahasiswa',
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
            'key' => 'page-control-user'
        ])

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nama, Email, atau ID Pengguna...',
            ])
        </div>

        <div class="order-3 sm:order-2 sm:col-span-3 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputProdiFilter',
                'xSearchResultsString' => 'prSearchResults',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan Program Studi...',
                'xSearchQueryString' => 'prSearchQuery',
                'selectedXId' => $selectedPrId,
                'selectedXName' => $pr_name,
                'resetXFilter' => 'resetProdiFilter()',
                'xSearchQuery' => $prSearchQuery,
                'xSearchResults' => $prSearchResults,
                'selectXForFilterString' => 'selectProdiForFilter',
                'typeXString' => 'prodi',
                'unfoundString' => 'Tidak ada Program Studi ditemukan!',
            ])
        </div>

        {{-- Tombol Reset Filter Utama --}}
        {{-- <div class="sm:col-span-1 relative">
            <button wire:click="resetAllFilters"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition duration-150 shadow-md whitespace-nowrap">
                <i class="fas fa-sync-alt mr-1"></i> Reset
            </button>
        </div> --}}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputJurusanFilter',
                'xSearchResultsString' => 'jrSearchResults',
                'iconString' => 'book-open',
                'placeholderString' => 'Filter berdasarkan Jurusan...',
                'xSearchQueryString' => 'jrSearchQuery',
                'selectedXId' => $selectedJrId,
                'selectedXName' => $jr_name,
                'resetXFilter' => 'resetJurusanFilter()',
                'xSearchQuery' => $jrSearchQuery,
                'xSearchResults' => $jrSearchResults,
                'selectXForFilterString' => 'selectJurusanForFilter',
                'typeXString' => 'jurusan',
                'unfoundString' => 'Tidak ada Jurusan ditemukan!',
            ])
        </div>

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputFakultasFilter',
                'xSearchResultsString' => 'fkSearchResults',
                'iconString' => 'building-library',
                'placeholderString' => 'Filter berdasarkan Fakultas...',
                'xSearchQueryString' => 'fkSearchQuery',
                'selectedXId' => $selectedFkId,
                'selectedXName' => $fk_name,
                'resetXFilter' => 'resetFakultasFilter()',
                'xSearchQuery' => $fkSearchQuery,
                'xSearchResults' => $fkSearchResults,
                'selectXForFilterString' => 'selectFakultasForFilter',
                'typeXString' => 'fakultas',
                'unfoundString' => 'Tidak ada Fakultas ditemukan!',
            ])
        </div>
    </div>
</div>
