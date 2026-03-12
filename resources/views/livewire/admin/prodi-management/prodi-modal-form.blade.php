<flux:modal name="prodi-modal" wire:model="showProdiModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full relative">

        @php
            $targetLoading = 'editProdi'
        @endphp

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveProdi, updateProdi">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>

        {{-- 1. Header Modal --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">

                <template x-if="$store.config.typeModal == 'prodi'" x-cloak>
                    <flux:badge icon="academic-cap" color="red" size="lg">
                        <span x-text="$store.config.isEdit ? 'Edit Program Studi' : 'Tambah Program Studi'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.config.typeModal == 'jurusan'" x-cloak>
                    <flux:badge icon="book-open" color="lime" size="lg">
                        <span x-text="$store.config.isEdit ? 'Edit Jurusan' : 'Tambah Jurusan'"></span>
                    </flux:badge>
                </template>

                <template x-if="$store.config.typeModal == 'fakultas'" x-cloak>
                    <flux:badge icon="building-library" color="cyan" size="lg">
                        <span x-text="$store.config.isEdit ? 'Edit Fakultas' : 'Tambah Fakultas'"></span>
                    </flux:badge>
                </template>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $prodiType --}}
            <form wire:submit.prevent="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}" enctype="multipart/form-data"
                id="prodiForm">

                <template x-if="$store.config.typeModal == 'prodi'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.prodi-input')
                </template>

                <template x-if="$store.config.typeModal == 'jurusan'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.jurusan-input')
                </template>

                <template x-if="$store.config.typeModal == 'fakultas'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.fakultas-input')
                </template>

                {{-- 3. Footer / Button Action --}}
                <div class="p-4 mt-8 bg-gray-50 rounded-lg border border-gray-100">
                    @include('livewire.admin.prodi-management.modal-form.message-form')
                    @include('livewire.admin.global.modal-form.button-form', [
                        'xType' => $prodiType,
                        'updateX' => 'updateProdi',
                        'saveX' => 'saveProdi',
                    ])
                </div>
            </form>
        </div>
    </div>
</flux:modal>
