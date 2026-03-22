<div x-data="{ activeTab: @entangle('filter') }" class="flex space-x-4 overflow-x-auto pb-2 md:pb-0">

    {{-- Tab Semua --}}
    @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'filterBy',
                'xFilter' => $filter,
                'tabFilter' => $totalTab,
                'tabString' => '',
                'tabNameString' => 'Semua ' . ucfirst($typeXString)
            ])

    {{-- Tab 1 --}}
    @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'filterBy',
                'xFilter' => $filter,
                'tabFilter' => $totalTab1,
                'tabString' => $tab1String,
                'tabNameString' => ucfirst($tab1String)
            ])
    {{-- Tab 2 --}}
    @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'filterBy',
                'xFilter' => $filter,
                'tabFilter' => $totalTab2,
                'tabString' => $tab2String,
                'tabNameString' => ucfirst($tab2String)
            ])
    {{-- Tab 3 --}}
    @include('livewire.admin.global.search-and-filters.partial.tab-filter', [
                'xString' => 'filterBy',
                'xFilter' => $filter,
                'tabFilter' => $totalTab3,
                'tabString' => $tab3String,
                'tabNameString' => ucfirst($tab3String)
            ])
</div>
