<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] flex flex-col-reverse border-b">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">
            {{-- Mata Kuliah --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalMK,
                'tabString' => '',
                'tabNameString' => 'Semua Mata Kuliah'
            ])
            {{-- Tab Tatap Muka --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalTatapMuka,
                'tabString' => 'mk-tatap-muka',
                'tabNameString' => 'Tatap Muka'
            ])
            {{-- Tab Praktikum --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalPraktikum,
                'tabString' => 'mk-praktikum',
                'tabNameString' => 'Praktikum'
            ])
            {{-- Tab Praktek Lapangan --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalPraktek,
                'tabString' => 'mk-praktek-lapangan',
                'tabNameString' => 'Praktek Lapangan'
            ])
            {{-- Tab Simulasi --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalSimulasi,
                'tabString' => 'mk-simulasi',
                'tabNameString' => 'Simulasi'
            ])
        </div>

    </div>
</div>