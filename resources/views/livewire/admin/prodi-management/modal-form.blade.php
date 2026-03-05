<flux:modal name="user-modal" wire:model="showUserModal" class="sm:w-full md:w-3xl max-w-4xl h-[98vh]">

    <div class="flex flex-col h-full">
        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">

                @php
                    $textShow = $isEditing ? 'Edit ' : 'Tambah ';

                    $colorRole = 'gray';
                    if ($roleType == 'admin') {
                        $colorRole = 'red';
                    } elseif ($roleType == 'dosen') {
                        $colorRole = 'lime';
                    } elseif ($roleType == 'mahasiswa') {
                        $colorRole = 'cyan';
                    } elseif ($roleType == 'file') {
                        $colorRole = 'green';
                    }
                @endphp

                @if ($roleType == 'admin')
                    <flux:badge icon="cog-6-tooth" color="red" size="lg">
                        {{ $textShow }}
                        Admin</flux:badge>
                @elseif ($roleType == 'dosen')
                    <flux:badge icon="briefcase" color="lime" size="lg">
                        {{ $textShow }}
                        Dosen</flux:badge>
                @elseif ($roleType == 'mahasiswa')
                    <flux:badge icon="book-open" color="cyan" size="lg">
                        {{ $textShow }}
                        Mahasiswa</flux:badge>
                @elseif ($roleType == 'file')
                    <flux:badge icon="table-cells" color="green" size="lg">
                        Input Data Pengguna dengan File Excel</flux:badge>
                @else
                    {{ $textShow }}Pengguna
                @endif

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