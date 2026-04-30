<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] flex flex-col-reverse border-b">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalUsers,
                'tabString' => '',
                'tabNameString' => 'Semua Pengguna'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalAdmins,
                'tabString' => 'admin',
                'tabNameString' => 'Admin'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalMahasiswas,
                'tabString' => 'mahasiswa',
                'tabNameString' => 'Mahasiswa'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalDosens,
                'tabString' => 'dosen',
                'tabNameString' => 'Dosen'
            ])
        </div>

    </div>
</div>