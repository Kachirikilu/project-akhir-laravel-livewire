<div x-data="{ activeTab: @entangle('switchTable') }" 
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] flex flex-col-reverse border-b">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">
            {{-- Program Studi --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalProdis,
                'tabString' => 'prodi',
                'tabNameString' => 'Program Studi'
            ])
            {{-- Tab Jurusan --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalJurusan,
                'tabString' => 'jurusan',
                'tabNameString' => 'Jurusan'
            ])
            {{-- Tab Fakultas --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalFakultas,
                'tabString' => 'fakultas',
                'tabNameString' => 'Fakultas'
            ])
        </div>

    </div>
</div>