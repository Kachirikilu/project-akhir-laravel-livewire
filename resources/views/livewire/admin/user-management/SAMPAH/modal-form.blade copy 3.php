<flux:modal name="user-modal" wire:model="showUserModal" class="sm:w-full md:w-3xl max-w-4xl h-[95vh]">

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
                @endif

            </h3>
        </div>

        {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
        <div class="p-6 pb-flex-1 overflow-y-auto space-y-6">
            <form wire:submit.prevent="{{ $isEditing ? 'updateUser' : 'saveUser' }}" id="userForm">

                {{-- ****************************************************** --}}
                {{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
                {{-- ****************************************************** --}}
                <div class="p-4 bg-white shadow-sm rounded-lg border border-gray-100 space-y-4">
                    <h4 class="text-lg font-medium text-gray-700 border-b pb-2">Account Information</h4>

                    {{-- 📧 Email Input --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email
                            <span class="text-red-500">*</span></label>

                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <flux:icon.envelope variant="mini" class="text-{{ $colorRole }}-700" />
                            </div>
                            <input wire:model.lazy="email" type="email" id="email" placeholder="contoh@domain.com"
                                class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @error('email')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 🔒 Password Input --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password
                            @if (!$isEditing)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <flux:icon.lock-closed variant="mini" class="text-{{ $colorRole }}-700" />
                            </div>
                            <input wire:model.lazy="password" type="password" id="password"
                                placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password' }}"
                                class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @error('password')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

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
                            <input wire:model.lazy="name" type="text" id="name"
                                placeholder="Masukkan Nama Lengkap"
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
                                <input wire:model.lazy="nip" type="text" id="nip"
                                    placeholder="Nomor Induk Pegawai (NIP)" inputmode="numeric" pattern="[0-9]*"
                                    maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            @error('nip')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($roleType == 'admin')
                            <div>
                                <label for="nitk" class="block text-sm font-medium text-gray-700">Lecturer
                                    ID (NITK)<span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <flux:icon.identification variant="mini"
                                            class="text-{{ $colorRole }}-700" />
                                    </div>
                                    <input wire:model.lazy="nitk" type="text" id="nitk"
                                        placeholder="Nomor Induk Tenaga Kependidikan (NITK)" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
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
                                    ID (NIDN) <span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <flux:icon.identification variant="mini"
                                            class="text-{{ $colorRole }}-700" />
                                    </div>
                                    <input wire:model.lazy="nidn" type="text" id="nidn"
                                        placeholder="Nomor Induk Dosen Nasional (NIDN)" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                        class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('nidn')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- 🆔 NIDK Input (Dosen) --}}
                            <div>
                                <label for="nidk" class="block text-sm font-medium text-gray-700">Lecturer
                                    ID (NIDK)<span class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <flux:icon.identification variant="mini"
                                            class="text-{{ $colorRole }}-700" />
                                    </div>
                                    <input wire:model.lazy="nidk" type="text" id="nidk"
                                        placeholder="Nomor Induk Dosen Khusus (NIDK)" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="20"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
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
                                <input wire:model.lazy="nim" type="text" id="nim"
                                    placeholder="Nomor Induk Mahasiswa (NIM)" inputmode="numeric" pattern="[0-9]*"
                                    maxlength="20"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                    class="w-full border rounded-lg pl-10 px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            @error('nim')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 📅 Tahun Masuk Input (Mahasiswa) --}}
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

                    <div class="relative" x-data="{ open: false }">
                        <label for="prodi_search" class="block text-sm font-medium text-gray-700">
                            Program Studi <span class="text-red-500">*</span>
                        </label>

                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <flux:icon.academic-cap variant="mini" class="text-{{ $colorRole }}-700" />
                            </div>

                            <input autocomplete="off" wire:model.live.debounce.300ms="prodi_name_search"
                                type="text" @focus="open = true; $event.target.select()"
                                @click.outside="open = false" @keydown.escape.window="open = false"
                                @keydown.enter.prevent="open = false" id="prodi_search"
                                placeholder="Cari Nama Program Studi"
                                class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

                            {{-- Tombol Reset --}}
                            @if ($prodi_id || strlen($prodi_name_search) > 0)
                                <button wire:click.prevent="resetProdiInput" type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-{{ $colorRole }}-700 hover:text-red-500 transition duration-150"
                                    title="Bersihkan Pilihan">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- INFO PRODI TERPILIH --}}
                        @if ($prodi_id && $prodi_name_search)
                            <p class="text-xs text-indigo-600 mt-1 font-medium italic">
                                Terpilih: {{ $prodi_name_search }} (ID: {{ $prodi_id }})
                            </p>
                        @endif

                        {{-- FLOATING RESULTS --}}
                        <div x-show="open && ($wire.prodi_name_search.length > 0 || $wire.prodi_results.length > 0)"
                            x-transition.opacity x-cloak
                            class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

                            @forelse ($prodi_results as $prodi)
                                <div wire:key="prodi-{{ $prodi['id'] }}"
                                    wire:click="selectProdi({{ $prodi['id'] }}, '{{ $prodi['prodi'] }}')"
                                    @click="open = false"
                                    class="px-4 py-3 cursor-pointer hover:bg-indigo-600 group transition duration-150 border-b border-gray-50 last:border-none">

                                    <div class="flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-semibold text-gray-800 group-hover:text-white leading-tight">
                                                {{ $prodi['prodi'] }}
                                            </span>
                                            <span class="text-xs text-gray-500 group-hover:text-indigo-100 mt-0.5">
                                                {{ $prodi['fakultas'] }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md text-{{ $colorRole }}-700 ml-2">
                                            ID: {{ $prodi['id'] }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                @if (strlen($prodi_name_search) > 0 && !$prodi_id)
                                    <div class="p-4 text-center">
                                        <p class="text-sm text-gray-500 italic">Data tidak ditemukan</p>
                                    </div>
                                @endif
                            @endforelse
                        </div>

                        {{-- ERROR MESSAGES --}}
                        @error('prodi_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>


                </div>

                {{-- 3. Footer/Tombol --}}
                <div class="p-4 mt-4 bg-gray-50 rounded-b-lg rounded-t-sm gap-4 shadow-sm">

                    {{-- 💡 Bagian Kiri (Error & Tips) --}}
                    <div class="flex-1 text-xs text-gray-600 space-y-3">

                        {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
                        @if ($errors->any())
                            <div class="p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                                <div class="flex items-center gap-2 mb-3">
                                    <flux:icon name="exclamation-triangle" variant="mini" class="text-red-600" />
                                    <h4 class="font-bold text-red-700 text-xs uppercase tracking-wider">
                                        Ada beberapa kesalahan:
                                    </h4>
                                </div>

                                <div class="space-y-2">
                                    @foreach ($errors->all() as $error)
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 shrink-0"></div>
                                            <p class="text-sm text-red-600 leading-relaxed">
                                                {{ $error }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 💡 2. Tips (Di bawah Error) --}}
                        <div class="rounded-xl border border-slate-200 bg-white/50 p-4 shadow-sm">
                            <div class="flex items-center gap-2 mb-3">
                                <flux:icon name="calendar" variant="mini" class="text-indigo-600" />
                                <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tips</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        Kosongkan kolom <strong class="text-slate-900 font-semibold">password</strong>
                                        untuk mempertahankan password lama (saat edit).
                                    </p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        Pastikan semua kolom <strong class="text-slate-900 font-semibold">wajib
                                            diisi</strong> dengan benar.
                                    </p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></div>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        Perubahan akan tersimpan segera setelah formulir dikirim.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- 💾 3. Tombol Aksi (Di sebelah Kanan, diatur ke flex-col-reverse agar Batal di atas Simpan di HP, namun Flex-row di Desktop) --}}
                        <div
                            class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-auto">

                            <div class="flex flex-col sm:flex-row gap-2 mt-2">
                                <flux:button type="submit" variant="primary"
                                    wire:click="{{ $isEditing ? 'updateUser' : 'saveUser' }}"
                                    wire:loading.attr="disabled"
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 border-none">
                                    <span wire:loading.remove
                                        wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                        {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                    </span>
                                    <span wire:loading wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                        Memproses...
                                    </span>
                                </flux:button>

                                <flux:modal.close>
                                    <flux:button variant="primary"
                                        class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 border-none">
                                        Batal
                                    </flux:button>
                                </flux:modal.close>

                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </div>

</flux:modal>
