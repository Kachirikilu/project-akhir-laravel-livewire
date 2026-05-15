<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.staff.obe-management.obe-toolbar', ["typeXString" => 'all'])
    @include('livewire.staff.obe-management.obe-switch-table')
    @include('livewire.staff.obe-management.obe-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        
        @if ($switchTable !== 'dosen')
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
        @else
            @include('livewire.admin.user-management.user-table', ['withRPS' => true])
            @include('livewire.admin.user-management.user-modal-delete')
        @endif
    </div>

    @include('livewire.staff.obe-management.rps-management.rps-modal-form')
    @include('livewire.staff.obe-management.rps-management.rps-show-modal')
    
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form')
    @include('livewire.staff.obe-management.ref-management.ref-modal-form')

    @include('livewire.admin.user-management.user-modal-form', ['withRPS' => true])
    
    @include('livewire.staff.obe-management.rps-management.rps-modal-delete')
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-delete')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-delete')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-delete')
    @include('livewire.staff.obe-management.ref-management.ref-modal-delete')
</div>