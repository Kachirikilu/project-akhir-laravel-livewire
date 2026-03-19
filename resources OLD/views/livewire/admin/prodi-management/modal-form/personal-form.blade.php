<div>
    {{-- ****************************************************** --}}
    {{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
    {{-- ****************************************************** --}}
    <div class="p-4 mt-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
        <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Personal Information
        </h4>

        {{-- 👤 Nama Input --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name
                <span class="text-red-500">*</span></label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon.user variant="mini" class="text-{{ $colorRole }}-700" />
                </div>
                <input wire:model.lazy="name" type="text" id="name" placeholder="Masukkan Nama Lengkap"
                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @error('name')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Input Khusus Berdasarkan Role Type --}}
        @if ($roleType === 'admin' || $roleType === 'dosen')
            {{-- 🆔 NIP Input (Admin/Dosen) --}}
            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700">Lecturer
                    ID (NIP) <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <flux:icon.identification variant="mini" class="text-{{ $colorRole }}-700" />
                    </div>
                    <input wire:model.lazy="nip" type="text" id="nip" placeholder="Nomor Induk Pegawai (NIP)"
                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                        class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @error('nip')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            @if ($roleType == 'admin')
                <div>
                    <label for="nitk" class="block text-sm font-medium text-gray-700">Lecturer
                        ID (NITK)</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <flux:icon.identification variant="mini" class="text-{{ $colorRole }}-700" />
                        </div>
                        <input wire:model.lazy="nitk" type="text" id="nitk"
                            placeholder="Nomor Induk Tenaga Kependidikan (NITK)" inputmode="numeric" pattern="[0-9]*"
                            maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                            class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('nitk')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            @else
                {{-- 🆔 NNIDN Input (Dosen) --}}
                <div>
                    <label for="nidn" class="block text-sm font-medium text-gray-700">Lecturer
                        ID (NIDN)</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <flux:icon.identification variant="mini" class="text-{{ $colorRole }}-700" />
                        </div>
                        <input wire:model.lazy="nidn" type="text" id="nidn"
                            placeholder="Nomor Induk Dosen Nasional (NIDN)" inputmode="numeric" pattern="[0-9]*"
                            maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                            class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('nidn')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 🆔 NIDK Input (Dosen) --}}
                <div>
                    <label for="nidk" class="block text-sm font-medium text-gray-700">Lecturer
                        ID (NIDK)</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <flux:icon.identification variant="mini" class="text-{{ $colorRole }}-700" />
                        </div>
                        <input wire:model.lazy="nidk" type="text" id="nidk"
                            placeholder="Nomor Induk Dosen Khusus (NIDK)" inputmode="numeric" pattern="[0-9]*"
                            maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                            class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @error('nidk')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        @elseif($roleType === 'mahasiswa')
            {{-- 🔢 NIM Input (Mahasiswa) --}}
            <div>
                <label for="nim" class="block text-sm font-medium text-gray-700">Student
                    ID (NIM) <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <flux:icon.identification variant="mini" class="text-{{ $colorRole }}-700" />
                    </div>
                    <input wire:model.lazy="nim" type="text" id="nim" placeholder="Nomor Induk Mahasiswa (NIM)"
                        inputmode="numeric" pattern="[0-9]*" maxlength="20"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                        class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @error('nim')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- 📅 Tahun Angkatan Input (Mahasiswa) --}}
            <div>
                <label for="tahun_angkatan" class="block text-sm font-medium text-gray-700">Entry
                    Year <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <flux:icon.calendar-days variant="mini" class="text-{{ $colorRole }}-700" />
                    </div>
                    <input wire:model.lazy="tahun_angkatan" type="number" id="tahun_angkatan"
                        placeholder="Contoh: 2022" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                        class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                @error('tahun_angkatan')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        @endif

        @include('livewire.admin.user-management.modal-form.prodi-input-form')

    </div>
</div>
