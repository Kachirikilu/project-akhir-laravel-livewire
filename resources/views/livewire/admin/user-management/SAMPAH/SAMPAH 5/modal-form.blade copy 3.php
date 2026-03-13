<flux:modal name="user-modal" wire:model="showUserModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full">

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveUser, updateUser">
            <div class="absolute inset-0 z-50 bg-white/70 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-indigo-600" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>

        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">
                <template x-if="$store.config?.typeModal == 'admin'" x-cloak>
                    <flux:badge icon="cog-6-tooth" color="red" size="lg">Tambah Pengguna Admin</flux:badge>
                </template>
                <template x-if="$store.config?.typeModal == 'dosen'" x-cloak>
                    <flux:badge icon="cog-6-tooth" color="lime" size="lg">Tambah Pengguna Dosen</flux:badge>
                </template>
                <template x-if="$store.config?.typeModal == 'mahasiswa'" x-cloak>
                    <flux:badge icon="cog-6-tooth" color="cyan" size="lg">Tambah Pengguna Mahasiswa</flux:badge>
                </template>
            </h3>
        </div>

        {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
        <div class="p-6 pb-flex-1 overflow-y-auto space-y-6">

            <form
                @if ($roleType == 'file')
                    wire:submit.present="saveAllRows"
                @else
                    wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}"
                @endif
                enctype="multipart/form-data" id="userForm">

                <template x-if="$store.config?.typeModal == 'file'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.excel-form', ['targetLoading' => 'editUser'])
                </template>

                <template x-if="$store.config?.typeModal !== 'file'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.account-form', ['targetLoading' => 'editUser'])
                </template>

                <template x-if="$store.config?.typeModal == 'admin'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.admin-form', ['targetLoading' => 'editUser'])
                </template>
                <template x-if="$store.config?.typeModal == 'dosen'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.dosen-form', ['targetLoading' => 'editUser'])
                </template>
                <template x-if="$store.config?.typeModal == 'mahasiswa'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.mahasiswa-form', ['targetLoading' => 'editUser'])
                </template>

                {{-- 3. Footer/Tombol --}}
                <div class="p-4 mt-4 bg-gray-50 rounded-b-lg rounded-t-sm gap-4 shadow-sm">

                    <div class="flex-1 text-xs text-gray-600 space-y-3">
                        @include('livewire.admin.user-management.modal-form.message-form')
                        @include('livewire.admin.user-management.modal-form.button-form')
                    </div>

                </div>
            </form>
        </div>

    </div>

</flux:modal>