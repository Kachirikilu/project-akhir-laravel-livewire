<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div
        class="border-[var(--border-table-color)] flex flex-col md:flex-row md:justify-between md:items-end border-b gap-3">

        <div class="flex justify-end md:order-2 pb-2">
            <flux:button @click="$wire.exportOBEExcel()" size="sm"
                class="cursor-pointer h-8 !text-xs !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 border border-emerald-200 transition-colors">
                <flux:icon name="printer" class="mr-1 h-3.5 w-3.5" />
                <span>Export Excel</span>
                <flux:icon wire:loading wire:target="exportOBEExcel" name="arrow-path"
                    class="animate-spin h-3.5 w-3.5 ml-2 dark:!text-emerald-600" />
            </flux:button>
        </div>

        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">
               @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalRPS,
                'tabString' => 'rps',
                'tabNameString' => 'RPS'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalCPMK,
                'tabString' => 'cpmk',
                'tabNameString' => 'CPMK'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalSCPMK,
                'tabString' => 'scpmk',
                'tabNameString' => 'Sub-CPMK'
            ])
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalCPL,
                'tabString' => 'cpl',
                'tabNameString' => 'CPL'
            ])
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
                'tabFilter' => $totalDosen,
                'tabString' => 'dosen',
                'tabNameString' => 'Dosen'
            ])
        </div>
    </div>



</div>
