<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] flex flex-col-reverse border-b">

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