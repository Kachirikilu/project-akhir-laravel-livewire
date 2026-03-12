<flux:modal name="prodi-modal"
    x-data="{ 
        prodiType: @entangle('prodiType'), 
        isEditing: @entangle('isEditing')
    }"
    wire:model="showProdiModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full relative">
        {{-- Loading Overlay --}}
        <div wire:loading wire:target="addProdi, editProdi" 
            class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>

        {{-- 1. Header Modal (Alpine x-show: Aman dari Error PHP) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">
                
                {{-- Program Studi --}}
                <div x-show="prodiType === 'prodi'" x-cloak>
                    <flux:badge icon="cog-6-tooth" color="red" size="lg">
                        <span x-text="(isEditing ? 'Edit ' : 'Tambah ') + 'Program Studi'"></span>
                    </flux:badge>
                </div>

                {{-- Jurusan --}}
                <div x-show="prodiType === 'jurusan'" x-cloak>
                    <flux:badge icon="briefcase" color="lime" size="lg">
                        <span x-text="(isEditing ? 'Edit ' : 'Tambah ') + 'Jurusan'"></span>
                    </flux:badge>
                </div>

                {{-- Fakultas --}}
                <div x-show="prodiType === 'fakultas'" x-cloak>
                    <flux:badge icon="book-open" color="cyan" size="lg">
                        <span x-text="(isEditing ? 'Edit ' : 'Tambah ') + 'Fakultas'"></span>
                    </flux:badge>
                </div>

                {{-- File Excel --}}
                <div x-show="prodiType === 'file'" x-cloak>
                    <flux:badge icon="table-cells" color="green" size="lg">
                        Input Program Studi dengan File Excel
                    </flux:badge>
                </div>
            </h3>
        </div>

        {{-- 2. Konten Formulir --}}
        <div class="p-6 flex-1 overflow-y-auto space-y-6">
            <form
                @if ($prodiType == 'file')
                    wire:submit.present="saveAllRows"
                @else
                    wire:submit.prevent="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}"
                @endif
                enctype="multipart/form-data" id="prodiForm">

                <div class="min-h-[300px]">
                    @if ($prodiType === 'file')
                        {{-- @include('livewire.admin.prodi-management.modal-form.excel-form') --}}
                    @elseif ($prodiType === 'prodi')
                        @include('livewire.admin.prodi-management.modal-form.prodi-form')
                    @elseif ($prodiType === 'jurusan')
                        @include('livewire.admin.prodi-management.modal-form.jurusan-form')
                    @else
                        @include('livewire.admin.prodi-management.modal-form.fakultas-form')
                    @endif
                </div>

                {{-- 3. Footer/Tombol --}}
                <div class="p-4 mt-8 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex-1 text-xs text-gray-600 space-y-3">
                        @include('livewire.admin.prodi-management.modal-form.message-form')
                        @include('livewire.admin.prodi-management.modal-form.button-form')
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>