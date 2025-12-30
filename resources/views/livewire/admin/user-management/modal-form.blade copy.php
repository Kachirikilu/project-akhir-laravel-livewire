<div x-show="show" class="fixed inset-0 bg-gray-900/40 flex justify-center items-center z-50">

    <div @click.outside="show = false"
        class="bg-white rounded-lg w-full max-w-4xl lg:w-4/5 transform transition-all duration-200 ease-out scale-100 max-h-[90vh] flex flex-col">

        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="p-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">
                {{ $isEditing ? 'Edit ' . ucfirst($roleType) : 'Tambah ' . ucfirst($roleType) }}
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
                        <input wire:model.lazy="email" type="email" id="email" placeholder="contoh@domain.com"
                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input wire:model.lazy="password" type="password" id="password"
                            placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan Password' }}"
                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input wire:model.lazy="name" type="text" id="name" placeholder="Masukkan Nama Lengkap"
                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Input Khusus Berdasarkan Role Type --}}
                    @if ($roleType === 'dosen')
                        {{-- 🆔 NIP Input (Dosen) --}}
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700">Lecturer
                                ID (NIP) <span class="text-red-500">*</span></label>
                            <input wire:model.lazy="nip" type="text" id="nip"
                                placeholder="Nomor Induk Pegawai (NIP)"
                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('nip')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($roleType === 'mahasiswa')
                        {{-- 🔢 NIM Input (Mahasiswa) --}}
                        <div>
                            <label for="nim" class="block text-sm font-medium text-gray-700">Student
                                ID (NIM) <span class="text-red-500">*</span></label>
                            <input wire:model.lazy="nim" type="text" id="nim"
                                placeholder="Nomor Induk Mahasiswa (NIM)"
                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('nim')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- 📅 Tahun Masuk Input (Mahasiswa) --}}
                        <div>
                            <label for="tahun_angkatan" class="block text-sm font-medium text-gray-700">Entry
                                Year <span class="text-red-500">*</span></label>
                            <input wire:model.lazy="tahun_angkatan" type="number" id="tahun_angkatan"
                                placeholder="Contoh: 2020"
                                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('tahun_angkatan')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- 🎓 Autocomplete Prodi Search --}}
                    <div class="relative" x-data="{
                        open: false,
                    }" <label for="prodi_search"
                        class="block text-sm font-medium text-gray-700">Program
                        Studi <span class="text-red-500">*</span></label>

                        {{-- KONTROL INPUT DAN TOMBOL RESET --}}
                        <div class="relative mt-1">
                            <input autocomplete="off" wire:model.live.debounce.300ms="prodi_name_search" type="text"
                                @focus="open = true; $event.target.select()" @click.outside="open = false"
                                @keydown.escape.window="open = false" @keydown.enter.prevent="open = false"
                                id="prodi_search" placeholder="Cari Nama Program Studi"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

                            {{-- Tombol Reset (Hanya muncul jika ada teks atau prodi sudah dipilih) --}}
                            @if ($prodi_id || strlen($prodi_name_search) > 0)
                                <button wire:click.prevent="resetProdiInput" type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
                                    title="Bersihkan Pilihan">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        {{-- AKHIR KONTROL INPUT --}}
                        @if ($prodi_id && $prodi_name_search)
                            <p class="text-xs text-gray-500 mt-1">
                                Prodi Terpilih: <span class="font-medium text-indigo-600">{{ $prodi_name_search }}
                                    (ID:
                                    {{ $prodi_id }})</span>
                            </p>
                        @endif

                        <input wire:model.defer="prodi_id" type="hidden">

                        @error('prodi_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                        @error('prodi_name_search')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror

                        {{-- <div x-show="open && prodiResultsCount >= 0" x-cloak @mousedown.prevent --}}
                        <div x-show="open" x-cloak
                            class="relative z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                            @forelse ($prodi_results as $prodi)
                                <div wire:key="prodi-{{ $prodi['id'] }}"
                                    wire:click="selectProdi({{ $prodi['id'] }}, '{{ $prodi['nama_prodi'] }}')"
                                    @click="open = false"
                                    class="px-4 py-2 cursor-pointer hover:bg-indigo-50 text-gray-800 transition duration-150">
                                    <span class="font-medium">{{ $prodi['nama_prodi'] }}</span>
                                    <span
                                        class="text-xs text-gray-500 hover:text-white float-right">{{ $prodi['fakultas'] }}
                                        - ID:
                                        {{ $prodi['id'] }}</span>
                                </div>
                            @empty
                                @if (strlen($prodi_name_search) > 0 && !$prodi_id)
                                    <p class="p-2 text-sm text-gray-500">Tidak ada Prodi yang
                                        ditemukan.
                                    </p>
                                @endif
                            @endforelse
                        </div>

                        @if (strlen($prodi_name_search) >= 2 && empty($prodi_results) && !$prodi_id)
                            <p x-show="!open" class="text-sm text-gray-500 mt-1">Tidak ada Prodi yang
                                cocok.
                            </p>
                        @endif
                    </div>
                    {{-- End Autocomplete --}}
                </div>

                {{-- 3. Footer/Tombol --}}
                <div class="p-4 mt-4 border-t bg-gray-50 rounded-b-lg gap-4">

                    {{-- 💡 Bagian Kiri (Error & Tips) --}}
                    <div class="flex-1 text-xs text-gray-600 space-y-3">

                        {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
                        @if ($errors->any())
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <h4 class="font-semibold text-red-700 mb-2">⚠️ Ada beberapa kesalahan:
                                </h4>
                                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- 💡 2. Tips (Di bawah Error) --}}
                        <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                            <span class="font-semibold text-gray-700 block mb-1">💡 Tips:</span>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                <li>Kosongkan kolom **password** untuk mempertahankan password
                                    lama (saat edit).</li>
                                <li>Pastikan semua kolom **wajib diisi** dengan benar.</li>
                                <li>Perubahan akan tersimpan segera setelah formulir dikirim.</li>
                            </ul>
                        </div>

                        {{-- 💾 3. Tombol Aksi (Di sebelah Kanan, diatur ke flex-col-reverse agar Batal di atas Simpan di HP, namun Flex-row di Desktop) --}}
                        <div
                            class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-auto">

                            {{-- Tombol submit --}}
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto disabled:opacity-50"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                    {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                </span>
                                <span wire:loading wire:target="{{ $isEditing ? 'updateUser' : 'saveUser' }}">
                                    Memproses...
                                </span>
                            </button>

                            {{-- Tombol Batal --}}
                            <button @click.prevent="show = false" type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>
