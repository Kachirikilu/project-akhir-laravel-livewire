{{-- Loading Overlay --}}
{{-- <div wire:loading wire:target="saveCPL, updateCPL">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div> --}}

<div class="flex flex-col h-full relative">


    {{-- 1. Header Modal --}}
    <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

        <h3 class="text-xl font-semibold">

            <flux:badge icon="clipboard-document-list" color="red" size="lg">
                <span
                    x-text="$store.cpl?.isEdit ? 'Edit OBE - Capaian Pembelajaran Lulusan' : 'Tambah OBE - Capaian Pembelajaran Lulusan'"></span>
            </flux:badge>

        </h3>
    </div>

    {{-- 2. Konten & Form --}}
    <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
        <form x-on:submit.prevent="$wire.{{ $isEditingCPL ? 'updateCPL' : 'saveCPL' }}($store.cpl)"
            enctype="multipart/form-data" id="cplForm">

            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input')

            <div
                class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-message-form', ['show' => $showCPLModal])
                    @include('livewire.global.modal-form.button-form', [
                        'targetX' => 'addCPL, saveCPL, editCPL, updateCPL',
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
