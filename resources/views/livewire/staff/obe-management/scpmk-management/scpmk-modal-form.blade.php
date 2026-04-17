{{-- Loading Overlay --}}
{{-- <div wire:loading wire:target="saveSCPMK, updateSCPMK">
        <div class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div> --}}

<div class="flex flex-col h-full relative">


    {{-- 1. Header Modal --}}
    <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b">
        <h3 class="text-xl font-semibold">

            <flux:badge icon="academic-cap" color="indigo" size="lg">
                <span
                    x-text="$store.scpmk?.isEdit ? 'Edit OBE - Sub Capaian Pembelajaran Mata Kuliah' : 'Tambah OBE - Sub Capaian Pembelajaran Mata Kuliah'"></span>
            </flux:badge>

        </h3>
    </div>

    {{-- 2. Konten & Form --}}
    <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
        <form x-on:submit.prevent="$wire.{{ $isEditingSCPMK ? 'updateSCPMK' : 'saveSCPMK' }}($store.scpmk)"
            enctype="multipart/form-data" id="scpmkForm">

            @include('livewire.staff.obe-management.scpmk-management.modal-form.scpmk-input')

            <div
                class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                    @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-message-form', ['show' => $showSCPMKModal])
                    @include('livewire.global.modal-form.button-form', [
                        'targetX' => 'addSCPMK, saveSCPMK, editSCPMK, updateSCPMK',
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
