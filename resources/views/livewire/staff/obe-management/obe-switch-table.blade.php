<div x-data="{ activeTab: @entangle('switchTable') }" 
     class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] flex flex-col-reverse border-b">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">
            {{-- Mata Kuliah --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalRPS,
                'tabString' => 'rps',
                'tabNameString' => 'RPS'
            ])
            {{-- Tab Tatap Muka --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalCPMK,
                'tabString' => 'cpmk',
                'tabNameString' => 'CPMK'
            ])
            {{-- Tab Praktikum --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalSCPMK,
                'tabString' => 'scpmk',
                'tabNameString' => 'Sub-CPMK'
            ])
            {{-- Tab Praktek Lapangan --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalCPL,
                'tabString' => 'cpl',
                'tabNameString' => 'CPL'
            ])
            {{-- Tab Simulasi --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalRef,
                'tabString' => 'ref',
                'tabNameString' => 'Referensi'
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalPiv,
                'tabString' => 'ref',
                'tabNameString' => 'Pivot'
            ])
        </div>

    </div>
</div>