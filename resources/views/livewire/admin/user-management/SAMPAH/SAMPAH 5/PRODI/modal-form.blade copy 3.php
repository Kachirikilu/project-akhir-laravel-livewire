<flux:modal name="prodi-modal" wire:model="showProdiModal" x-data="{
    pType: @entangle('prodiType').live,
    isEdit: @entangle('isEditing').live
}"
    @setup-modal.window="pType = $event.detail.prodiType" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    @php
        $colorIcon = 'text-red-700';
    @endphp

    <div class="flex flex-col h-full relative">

        {{-- Loading Overlay --}}
        {{-- <div wire:loading wire:target="addProdi, editProdi, saveProdi, updateProdi">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div> --}}

        {{-- 1. Header Modal --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">
                <template x-if="pType === 'prodi'">
                    <flux:badge icon="cog-6-tooth" color="red" size="lg"
                        x-text="(isEdit ? 'Edit ' : 'Tambah ') + 'Program Studi'"></flux:badge>
                </template>
                <template x-if="pType === 'jurusan'">
                    <flux:badge icon="briefcase" color="lime" size="lg"
                        x-text="(isEdit ? 'Edit ' : 'Tambah ') + 'Jurusan'"></flux:badge>
                </template>
                <template x-if="pType === 'fakultas'">
                    <flux:badge icon="building-library" color="cyan" size="lg"
                        x-text="(isEdit ? 'Edit ' : 'Tambah ') + 'Fakultas'"></flux:badge>
                </template>
                <template x-if="pType === 'file'">
                    <flux:badge icon="document-text" color="green" size="lg">Import Excel</flux:badge>
                </template>
            </h3>
        </div>
        

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $prodiType --}}
            <form wire:submit.prevent="handleSubmit" enctype="multipart/form-data" id="prodiForm">

                <div class="text-red-500 bg-yellow-100 p-2 text-xs">
                    Debug pType: <span x-text="pType"></span>
                </div>

                {{-- <template x-if="pType == 'prodi'" x-cloak> --}}
                    @include('livewire.admin.prodi-management.modal-form.prodi-form')
                {{-- </template> --}}

                <template x-if="pType == 'jurusan'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.jurusan-form')
                </template>

                <template x-if="pType == 'fakultas'" x-cloak>
                    @include('livewire.admin.prodi-management.modal-form.fakultas-form')
                </template>

                <template x-if="pType == 'file'" x-cloak>
                    {{-- @include('livewire.admin.prodi-management.modal-form.excel-form') --}}
                </template>

                {{-- 3. Footer / Button Action --}}
                <div class="p-4 mt-8 bg-gray-50 rounded-lg border border-gray-100">
                    @include('livewire.admin.prodi-management.modal-form.message-form')
                    @include('livewire.admin.prodi-management.modal-form.button-form')
                </div>
            </form>
        </div>
    </div>
</flux:modal>
