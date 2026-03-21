<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="mb-2 p-4 bg-white dark:bg-neutral-800 rounded-lg shadow-md border border-gray-100 dark:border-neutral-700 transition-colors duration-300">

    <div class="flex flex-col-reverse border-b dark:border-neutral-700 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
            {{-- Mata Kuliah --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalMatkuls,
                'tabString' => '',
                'tabNameString' => 'Semua Mata Kuliah'
            ])
            {{-- Tab Tatap Muka --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalTatapMuka,
                'tabString' => 'tatap_muka',
                'tabNameString' => 'Tatap Muka'
            ])
            {{-- Tab Praktikum --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalPraktikum,
                'tabString' => 'praktikum',
                'tabNameString' => 'Praktikum'
            ])
            {{-- Tab Praktek Lapangan --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalPraktekLapangan,
                'tabString' => 'praktek_lapangan',
                'tabNameString' => 'Praktek Lapangan'
            ])
            {{-- Tab Simulasi --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalSimulasi,
                'tabString' => 'simulasi',
                'tabNameString' => 'Simulasi'
            ])
        </div>

    </div>
</div>