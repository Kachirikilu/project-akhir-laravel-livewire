<flux:modal name="mk-modal" wire:model="showMKModal" x-data @mk-saved.window="$store.config.reset()"
    class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full relative">

        {{-- @php
            $targetLoading = 'editMK'
        @endphp --}}

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveMK, updateMK">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>

        {{-- 1. Header Modal --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold">

                <template x-if="$store.config?.typeModal == 'mk'" x-cloak>
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
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $mkType --}}
            <form x-on:submit.prevent="$wire.{{ $isEditing ? 'updateMK' : 'saveMK' }}($store.config)"
                enctype="multipart/form-data" id="mkForm">

                <template x-if="$store.config?.typeModal == 'mk'" x-cloak>
                    @include('livewire.admin.mk-management.modal-form.mk-input')
                </template>

                <template x-if="$store.config?.typeModal == 'jurusan'" x-cloak>
                    @include('livewire.admin.mk-management.modal-form.jurusan-input')
                </template>

                <template x-if="$store.config?.typeModal == 'fakultas'" x-cloak>
                    @include('livewire.admin.mk-management.modal-form.fakultas-input')
                </template>

                {{-- 3. Footer / Button Action --}}
                <div
                    class="p-4 mt-4 bg-gray-50 dark:bg-neutral-900/50 rounded-b-lg rounded-t-sm gap-4 shadow-sm border-t dark:border-neutral-700/50 transition-colors duration-300">

                    <div class="flex-1 text-xs text-gray-600 dark:text-gray-400 space-y-3">
                        @include('livewire.admin.mk-management.modal-form.message-form')
                        @include('livewire.admin.global.modal-form.button-form', [
                            'xType' => $mkType,
                            'targetX' => 'addMK, saveMK, editMK, updateMK',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
