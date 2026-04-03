<flux:modal name="rps-modal" wire:model="showRPSModal" x-data @rps-saved.window="$store.rps.reset()"
    class="sm:w-full md:w-3xl max-w-4xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveRPS, updateRPS">
        <div class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>

    <div class="flex flex-col h-full relative">

        {{-- @php
            $targetLoading = 'editRPS'
        @endphp --}}

        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b">
            <h3 class="text-xl font-semibold">

                {{-- <template x-if="$store.rps?.typeModal == 'mk-prodi'" x-cloak> --}}
                    <flux:badge icon="academic-cap" color="emerald" size="lg">
                        <span x-text="$store.rps?.isEdit ? 'Edit OBE - Rencana Pembelajaran Semester' : 'Tambah OBE - Rencana Pembelajaran Semester'"></span>
                    </flux:badge>
                {{-- </template> --}}

                {{-- <template x-if="$store.rps?.typeModal == 'mk-jurusan'" x-cloak>
                    <flux:badge icon="book-open" color="amber" size="lg">
                        <span x-text="$store.rps?.isEdit ? 'Edit Mata Kuliah - Jurusan' : 'Mata Kuliah - Jurusan'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.rps?.typeModal == 'mk-fakultas'" x-cloak>
                    <flux:badge icon="building-library" color="indigo" size="lg">
                        <span x-text="$store.rps?.isEdit ? 'Edit Mata Kuliah - Fakultas' : 'Mata Kuliah - Fakultas'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.rps?.typeModal == 'mk-universitas'" x-cloak>
                    <flux:badge icon="building-library" color="yellow" size="lg">
                        <span x-text="$store.rps?.isEdit ? 'Edit Mata Kuliah - Universitas' : 'Mata Kuliah - Universitas'"></span>
                    </flux:badge>
                </template> --}}

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $mkType --}}
            <form x-on:submit.prevent="$wire.{{ $isEditing ? 'updateRPS' : 'saveRPS' }}($store.rps)"
                enctype="multipart/form-data" id="mkForm">

                @include('livewire.staff.rps-management.modal-form.rps-input')

                {{-- 3. Footer / Button Action --}}
                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    {{-- dark:bg-neutral-900/50 dark:border-neutral-700/50  --}}
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.staff.rps-management.modal-form.rps-message-form')
                        @include('livewire.global.modal-form.button-form', [
                            'xType' => $mkType,
                            'targetX' => 'addRPS, saveRPS, editRPS, updateRPS',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
