{{-- Loading Overlay --}}
{{-- <div wire:loading wire:target="saveRef, updateRef">
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

            <flux:badge icon="clipboard-document-list" color="purple" size="lg">
                <span x-text="$store.ref?.isEdit ? 'Edit OBE - Referensi' : 'Tambah OBE - Referensi'"></span>
            </flux:badge>

        </h3>
    </div>

    {{-- 2. Konten & Form --}}
    <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
        <form x-on:submit.prevent="$wire.{{ $isEditingRef ? 'updateRef' : 'saveRef' }}($store.ref)"
            enctype="multipart/form-data" id="refForm">

            @include('livewire.staff.obe-management.ref-management.ref-modal-form.ref-input')

            <div
                class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                    @include('livewire.staff.obe-management.ref-management.ref-modal-form.ref-message-form', ['show' => $showRefModal])
                    @include('livewire.global.modal-form.button-form', [
                        'targetX' => 'addRef, saveRef, editRef, updateRef',
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
