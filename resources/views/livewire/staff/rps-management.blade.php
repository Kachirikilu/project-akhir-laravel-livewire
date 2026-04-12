<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.staff.rps-management.obe-toolbar')
    @include('livewire.staff.rps-management.obe-switch-table')
    @include('livewire.staff.rps-management.obe-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.staff.rps-management.obe-table', [
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

    @include('livewire.staff.rps-management.rps-flyout')
    @include('livewire.staff.rps-management.cpmk-modal-form')
    {{-- @include('livewire.staff.rps-management.rps-modal-delete') --}}
</div>