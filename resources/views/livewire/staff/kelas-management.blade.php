<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.staff.obe-management.obe-toolbar', ["typeXString" => 'all', 'isFlyout' => false])
    @include('livewire.staff.obe-management.obe-switch-table')
    @include('livewire.staff.obe-management.obe-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.staff.obe-management.obe-table', [
            'xResults' => match ($this->switchTable) {
                'rps' => $rps,
                'cpmk' => $cpmk,
                'scpmk' => $scpmk,
                'cpl' => $cpl,
                'ref' => $ref,
                // 'dosen' => $dosen,
                default => collect([]),
            },
            'xNameString' => match ($this->switchTable) {
                'rps' => 'RPS',
                'cpmk' => 'CPMK',
                'scpmk' => 'Sub-CPMK',
                'cpl' => 'CPL',
                'ref' => 'Referensi',
                // 'dosen' => 'Dosen',
                default => 'Data',
            },
        ])
    </div>

    @include('livewire.staff.obe-management.rps-management.rps-flyout')
    @include('livewire.staff.obe-management.rps-management.rps-show-modal')
    
    @include('livewire.staff.obe-management.cpmk-management.cpmk-flyout')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-flyout')
    @include('livewire.staff.obe-management.cpl-management.cpl-flyout')
    @include('livewire.staff.obe-management.ref-management.ref-flyout')
    
    {{-- @include('livewire.staff.obe-management.rps-management.rps-modal-delete') --}}
</div>