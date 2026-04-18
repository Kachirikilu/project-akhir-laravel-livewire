<div x-data="{ activeTab: @entangle($filterString) }" class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1">

    {{-- Tab Semua --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => $filterByFunc,
                'xFilter' => $filterString,
                'tabFilter' => $totalTab ?? null,
                'tabString' => '',
                'tabNameString' => 'Semua ' . ucfirst($typeXString)
            ])

    {{-- Tab 1 --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => $filterByFunc,
                'xFilter' => $filterString,
                'tabFilter' => $totalTab1 ?? null,
                'tabString' => $tab1String ?? null,
                'tabNameString' => $tab1Name ?? ucfirst($tab1String)
            ])

    {{-- Tab 2 --}}
    @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => $filterByFunc,
                'xFilter' => $filterString,
                'tabFilter' => $totalTab2 ?? null,
                'tabString' => $tab2String ?? null,
                'tabNameString' => $tab2Name ?? ucfirst($tab2String)
            ])

    @if ($tab3String ?? null)
    {{-- Tab 3 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => $filterByFunc,
                    'xFilter' => $filterString,
                    'tabFilter' => $totalTab3 ?? null,
                    'tabString' => $tab3String ?? null,
                    'tabNameString' => $tab3Name ?? ucfirst($tab3String ?? null)
                ])
    @endif

    @if ($tab4String ?? null)
    {{-- Tab 4 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => $filterByFunc,
                    'xFilter' => $filterString,
                    'tabFilter' => $totalTab4 ?? null,
                    'tabString' => $tab4String ?? null,
                    'tabNameString' => $tab4Name ?? ucfirst($tab4String ?? null)
                ])
    @endif

    @if ($tab5String ?? null)
    {{-- Tab 5 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => $filterByFunc,
                    'xFilter' => $filterString,
                    'tabFilter' => $totalTab5 ?? null,
                    'tabString' => $tab5String ?? null,
                    'tabNameString' => $tab5Name ?? ucfirst($tab5String ?? null)
                ])
    @endif

    @if ($tab6String ?? null)
    {{-- Tab 6 --}}
        @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => $filterByFunc,
                    'xFilter' => $filterString,
                    'tabFilter' => $totalTab6 ?? null,
                    'tabString' => $tab6String ?? null,
                    'tabNameString' => $tab6Name ?? ucfirst($tab6String ?? null)
                ])
    @endif
</div>
