{{-- @if (!$this->showCPLModal && !$this->showRefModal)
    <template x-if="$store.cpmk?.isFlyout == 1">
        <flux:modal name="cpmk-modal" wire:model="showCPMKModal" x-data @refresh-data-cpmk.window="$store.cpmk.reset()"
            flyout
            class="md:w-[90vw] max-w-3xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
        </flux:modal>
    </template>
@endif

<template x-if="$store.cpmk?.isFlyout == 0"> --}}
    <flux:modal name="cpmk-modal" wire:model="showCPMKModal" x-data @refresh-data-cpmk.window="$store.cpmk.reset()"
   class="md:w-[90vw] max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
    </flux:modal>
{{-- </template> --}}
