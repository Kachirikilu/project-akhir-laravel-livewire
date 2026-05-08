<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div
        class="border-[var(--border-table-color)] flex flex-col md:flex-row md:justify-between md:items-end border-b gap-3">

        <div class="flex justify-end md:order-2 pb-2">
            <flux:button @click="$wire.exportProdiExcel()" size="sm"
                class="cursor-pointer h-8 !text-xs !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 border border-emerald-200 transition-colors">
                <flux:icon name="printer" class="mr-1 h-3.5 w-3.5" />
                <span>Export Excel</span>
                <flux:icon wire:loading wire:target="exportProdiExcel" name="arrow-path"
                    class="animate-spin h-3.5 w-3.5 ml-2 dark:!text-emerald-600" />
            </flux:button>
        </div>

        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">
            {{-- Program Studi --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalProdis,
                'tabString' => 'prodi',
                'tabNameString' => 'Program Studi',
            ])
            {{-- Tab Departemen --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalDepartemen,
                'tabString' => 'departemen',
                'tabNameString' => 'Departemen',
            ])
            {{-- Tab Fakultas --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalFakultas,
                'tabString' => 'fakultas',
                'tabNameString' => 'Fakultas',
            ])
        </div>
    </div>
</div>