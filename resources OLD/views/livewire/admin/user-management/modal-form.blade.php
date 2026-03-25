<flux:modal name="user-modal" wire:model="showUserModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    <div class="flex flex-col h-full">
        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold">

                @php
                    $textShow = $isEditing ? 'Edit ' : 'Tambah ';

                    match($roleType) {
                        'admin' => [$colorIcon = 'text-red-700', $colorBadge = 'red', $textShow .= 'Admin'],
                        'dosen' => [$colorIcon = 'text-lime-700', $colorBadge = 'lime', $textShow .= 'Dosen'],
                        'mahasiswa' => [$colorIcon = 'text-cyan-700', $colorBadge = 'cyan', $textShow .= 'Mahasiswa'],
                        'file' => [$colorIcon = 'text-green-700', $colorBadge = 'green', $textShow = 'Input Data Pengguna dengan File Excel'],
                        default => [$colorIcon = 'text-gray-700', $colorBadge = 'gray', $textShow .= 'Pengguna'],
                    }
                @endphp

                <flux:badge icon="cog-6-tooth" color="{{ $colorBadge }}" size="lg">{{ $textShow }}</flux:badge>

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

                @if ($roleType === 'file')
                    @include('livewire.admin.user-management.modal-form.excel-form')
                @else
                    @include('livewire.admin.user-management.modal-form.account-form')
                    @include('livewire.admin.user-management.modal-form.personal-form')
                @endif

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