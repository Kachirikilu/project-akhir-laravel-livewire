<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="mb-2 p-4 bg-white dark:bg-neutral-800 rounded-lg shadow-md border border-gray-100 dark:border-neutral-700 transition-colors duration-300">

    <div class="flex flex-col-reverse border-b dark:border-neutral-700 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
            {{-- Program Studi --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalProdis,
                'tabString' => 'prodi',
                'tabNameString' => 'Program Studi'
            ])
            {{-- Tab Jurusan --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalJurusan,
                'tabString' => 'jurusan',
                'tabNameString' => 'Jurusan'
            ])
            {{-- Tab Fakultas --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalFakultas,
                'tabString' => 'fakultas',
                'tabNameString' => 'Fakultas'
            ])
        </div>

    </div>
</div>