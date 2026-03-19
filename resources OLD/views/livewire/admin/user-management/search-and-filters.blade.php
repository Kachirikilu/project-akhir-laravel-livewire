<div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
    <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.admin.global.search-and-filters.filter-mode', [
            'typeOfXString' => 'pengguna',
            'totalTab' => $totalUsers,
            'totalTab1' => $totalAdmins,
            'totalTab2' => $totalDosens,
            'totalTab3' => $totalMahasiswas,
            'tab1String' => 'admin',
            'tab2String' => 'dosen',
            'tab3String' => 'mahasiswa',
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.admin.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100],
        ])

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.admin.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nama, Email, atau ID Pengguna...',
            ])
        </div>

        <div class="order-3 sm:order-2 sm:col-span-3 relative">
            @include('livewire.admin.global.search-and-filters.secondary-search', [
                'selectedXNameString' => 'selectedProdiName',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan Program Studi...',
                'xSearchQueryString' => 'prodiSearchQuery',
                'selectedXId' => $selectedProdiId,
                'selectedXName' => $selectedProdiName,
                'resetXFilterString' => 'resetProdiFilter',
                'xSearchQuery' => $prodiSearchQuery,
                'xSearchResults' => $prodiSearchResults,
                'selectXForFilterString' => 'selectProdiForFilter',
                'typeOfXString' => 'prodi',
                'unfoundString' => 'Tidak ada program studi ditemukan!',
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
</div>
