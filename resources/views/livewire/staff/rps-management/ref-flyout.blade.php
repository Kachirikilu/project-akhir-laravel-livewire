@if ($this->showRPSModal || $this->showCPMKModal || $this->showSCPMKModal)
<template x-if="$store.ref?.isFlyout == 1">
    <flux:modal name="ref-modal" wire:model="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()"
        flyout
        class="md:w-[90vw] max-w-2xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.ref-modal-form')
    </flux:modal>
</template>
@endif

<template x-if="$store.ref?.isFlyout == 0">
    <flux:modal name="ref-modal" wire:model="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()"
        class="md:w-[90vw] max-w-3xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.ref-modal-form')
    </flux:modal>
</template>
