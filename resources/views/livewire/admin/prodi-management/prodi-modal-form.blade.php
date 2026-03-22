<flux:modal name="prodi-modal" wire:model="showProdiModal" x-data @prodi-saved.window="$store.config.reset()"
    class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full relative">

        {{-- @php
            $targetLoading = 'editProdi'
        @endphp --}}

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveProdi, updateProdi">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>

        {{-- 1. Header Modal --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold">

                <template x-if="$store.config?.typeModal == 'prodi'" x-cloak>
                    <flux:badge icon="academic-cap" color="emerald" size="lg">
                        <span x-text="$store.config?.isEdit ? 'Edit Program Studi' : 'Tambah Program Studi'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.config?.typeModal == 'jurusan'" x-cloak>
                    <flux:badge icon="book-open" color="amber" size="lg">
                        <span x-text="$store.config?.isEdit ? 'Edit Jurusan' : 'Tambah Jurusan'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.config?.typeModal == 'fakultas'" x-cloak>
                    <flux:badge icon="building-library" color="indigo" size="lg">
                        <span x-text="$store.config?.isEdit ? 'Edit Fakultas' : 'Tambah Fakultas'"></span>
                    </flux:badge>
                </template>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $prodiType --}}
            <form x-on:submit.prevent="$wire.{{ $isEditing ? 'updateProdi' : 'saveProdi' }}($store.config)"
                enctype="multipart/form-data" id="prodiForm">

                <template x-if="$store.config?.typeModal == 'prodi'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.prodi-input')
                </template>

                <template x-if="$store.config?.typeModal == 'jurusan'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.jurusan-input')
                </template>

                <template x-if="$store.config?.typeModal == 'fakultas'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.fakultas-input')
                </template>

                {{-- 3. Footer / Button Action --}}
               <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    {{-- dark:bg-neutral-900/50 dark:border-neutral-700/50  --}}
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.admin.prodi-management.modal-form.message-form')
                        @include('livewire.admin.global.modal-form.button-form', [
                            'xType' => $prodiType,
                            'targetX' => 'addProdi, saveProdi, editProdi, updateProdi',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
