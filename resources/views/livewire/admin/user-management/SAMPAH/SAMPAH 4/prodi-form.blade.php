<flux:modal name="prodi-modal" wire:model="showProdiModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    @php
        $colorIcon = 'text-red-700';
    @endphp

    <div class="flex flex-col h-full relative">


        {{-- 1. Header Modal --}}
        {{-- <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">
                    <flux:badge icon="cog-6-tooth" color="red" size="lg"
                        x-text="(isEdit ? 'Edit ' : 'Tambah ') + 'Program Studi'"></flux:badge>
            </h3>
        </div> --}}
        

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $prodiType --}}
            <form wire:submit.prevent="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}" enctype="multipart/form-data" id="prodiForm">
                
                <div class="text-red-500 bg-yellow-100 p-2 text-xs">
                    Debug: <span x-text="$store.config?.type"></span>
                </div>

                @include('livewire.admin.prodi-management.modal-form.prodi-form')

                {{-- 3. Footer / Button Action --}}
                <div class="p-4 mt-8 bg-gray-50 rounded-lg border border-gray-100">
                    @include('livewire.admin.prodi-management.modal-form.message-form')
                    @include('livewire.admin.prodi-management.modal-form.button-form')
                </div>
            </form>
        </div>
    </div>
</flux:modal>
