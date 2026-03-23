<flux:modal name="user-modal" wire:model="showUserModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full">

        {{-- @php
            $targetLoading = 'editUser'
        @endphp --}}

        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveUser, updateUser, saveAllRows, saveUserInternal">
            <div class="absolute inset-0 z-50 bg-black/60 flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>

        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold">
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
                @if ($roleType == 'file') wire:submit.prevent="saveAllRows"
                @else
                    x-on:submit.prevent="$wire.{{ $isEditing ? 'updateUser' : 'saveUser' }}($store.config)" @endif
                enctype="multipart/form-data" id="userForm">

                <template x-if="$store.config?.typeModal == 'file'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.excel-input')
                </template>

                <template x-if="$store.config?.typeModal !== 'file'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.user-input')
                </template>

                <template x-if="$store.config?.typeModal == 'admin'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.admin-input')
                </template>
                <template x-if="$store.config?.typeModal == 'dosen'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.dosen-input')
                </template>
                <template x-if="$store.config?.typeModal == 'mahasiswa'" x-cloak>
                    @include('livewire.admin.user-management.modal-form.mahasiswa-input')
                </template>

                {{-- 3. Footer/Tombol --}}
               <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    {{-- dark:bg-neutral-900/50 dark:border-neutral-700/50  --}}
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.admin.user-management.modal-form.user-message-form')

                        @include('livewire.admin.global.modal-form.button-form', [
                            'xType' => $roleType,
                            'targetX' => 'addUser, saveUser, editUser, updateUser',
                        ])
                    </div>

                </div>
            </form>
        </div>

    </div>

</flux:modal>
