<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout
    class="md:w-[95vw] max-w-7xl h-[98vh] !p-8 scrollbar-large">

    <flux:button @click="$wire.printPDF($store.rps?.id ?? null)"
        class="mb-8 cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 border border-emerald-200">

        <flux:icon name="printer" class="mr-2 h-4 w-4" />

        <div class="flex justify-between items-center w-full">
            <span>Print PDF RPS - <span x-text="$store.rps?.nama_rps"></span></span>
            <flux:icon wire:loading wire:target="printPDF()" name="arrow-path"
                class="animate-spin h-4 w-4 ml-2 dark:!text-emerald-600" />
        </div>
    </flux:button>

    <div class="p-4 relative bg-white rounded-md border-2">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'showRPS'])

        @include('livewire.staff.obe-management.rps-management.rps-show.rps-pdf-table')
    </div>
</flux:modal>
