<template x-if="$store.rps?.isFlyout == 1">
    <flux:modal name="rps-modal" wire:model="showRPSModal" x-data @refresh-data-rps.window="$store.rps.reset()"
        flyout
        class="md:w-[90vw] max-w-3xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.rps-modal-form')
    </flux:modal>
</template>

<template x-if="$store.rps?.isFlyout == 0">
    <flux:modal name="rps-modal" wire:model="showRPSModal" x-data @refresh-data-rps.window="$store.rps.reset()"
        class="md:w-[90vw] max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
        @include('livewire.staff.rps-management.rps-modal-form')
    </flux:modal>
</template>
