<template x-if="$store.cpl?.isFlyout == 1">
    <flux:modal name="cpl-modal" wire:model="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()"
        flyout
        class="md:w-[90vw] max-w-2xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.cpl-modal-form')
    </flux:modal>
</template>

<template x-if="$store.cpl?.isFlyout == 0">
    <flux:modal name="cpl-modal" wire:model="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()"
        class="md:w-[90vw] max-w-3xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.cpl-modal-form')
    </flux:modal>
</template>
