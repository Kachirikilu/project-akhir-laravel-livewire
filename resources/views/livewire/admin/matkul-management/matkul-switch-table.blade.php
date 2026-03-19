<div x-data="{ activeTab: @entangle('switchTable') }" class="mb-2 p-4 bg-white rounded-lg shadow-md border border-gray-100">

    <div class="flex flex-col-reverse border-b gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0">
            {{-- Mata Kuliah --}}
            @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalMatkuls,
                'tabString' => 'matkuls',
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

