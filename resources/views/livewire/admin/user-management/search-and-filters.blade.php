<div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
    <div class="flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">

        {{-- Bagian Tab / Link (Kiri) --}}
        <div class="flex space-x-4 overflow-x-auto pb-2 md:pb-0">
            {{-- Tab Semua --}}
            <button wire:click="filterBy('')"
                class="{{ isset($filter) && $filter == '' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-users mr-2"></i> Semua Pengguna (<span id="count-all">{{ $totalUsers }}</span>)
            </button>
            {{-- Tab Admin --}}
            <button wire:click="filterBy('admin')"
                class="{{ isset($filter) && $filter == 'admin' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-crown mr-2"></i> Admin (<span id="count-admin">{{ $totalAdmins }}</span>)
            </button>
            {{-- Tab Dosen --}}
            <button wire:click="filterBy('dosen')"
                class="{{ isset($filter) && $filter == 'dosen' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Dosen (<span id="count-dosen">{{ $totalDosens }}</span>)
            </button>
            {{-- Tab Mahasiswa --}}
            <button wire:click="filterBy('mahasiswa')"
                class="{{ isset($filter) && $filter == 'mahasiswa' ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                   tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
                <i class="fas fa-user-graduate mr-2"></i> Mahasiswa (<span
                    id="count-mahasiswa">{{ $totalMahasiswas }}</span>)
            </button>
        </div>

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        <div class="flex items-center justify-end pb-4 ml-4">

            {{-- <label class="text-sm font-medium text-gray-500 mr-2 whitespace-nowrap">Tampilkan:</label> --}}
            <div x-data="{ open: false, selected: @entangle('perPage').live }" class="relative w-15 **z-20**" @click.away="open = false">
                <button type="button" @click="open = !open"
                    class="flex items-center justify-between border border-gray-300 rounded-md shadow-sm 
                       py-1 px-2 text-sm w-full bg-white transition duration-150 hover:border-indigo-500">
                    <span x-text="selected">8</span>
                    <svg class="h-4 w-4 ml-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-cloak
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-20 mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-gray-300 ring-opacity-5 focus:outline-none overflow-hidden"
                    role="menu" aria-orientation="vertical" tabindex="-1">
                    @foreach ([3, 5, 8, 10, 15, 25, 50, 100] as $option)
                        <li wire:key="perPage-{{ $option }}" @click="selected = {{ $option }}; open = false"
                            class="text-gray-700 block px-3 py-1 text-sm cursor-pointer hover:bg-indigo-500 hover:text-white"
                            :class="{ 'bg-indigo-100 font-semibold text-indigo-700': selected == {{ $option }} }">
                            {{ $option }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <span class="text-sm font-medium text-gray-500 ml-2">baris</span>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-center w-full">
        <div class="sm:col-span-4 relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <flux:icon.magnifying-glass variant="mini" class="text-gray-400" />
            </div>
            <input wire:model.live="search" type="text" name="search" value="{{ $search ?? '' }}"
                placeholder="Cari Nama, Email, atau ID Pengguna..."
                class="w-full h-10 pl-10 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" />

            @if ($search)
                <button type="button" wire:click="resetInputFilter" $wire.search = ''; open=false"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
                    title="Bersihkan Filter">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            @endif
        </div>

        <div x-data="{ open: false, selectedName: @entangle('selectedProdiName').live }" class="sm:col-span-3 relative">

            <div class="relative w-full sm:flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <flux:icon.academic-cap variant="mini" class="text-gray-400" />
                </div>
                <input type="text" placeholder="Filter berdasarkan Program Studi..." x-model="selectedName"
                    wire:model.live="prodiSearchQuery" name="prodiSearchQuery"
                    @focus="open = true; $event.target.select()" @click.outside="open = false"
                    @keydown.escape.window="open = false" @keydown.enter.prevent="open = false"
                    class="w-full h-10 pl-10 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                    :class="{ 'pr-10': selectedName }" autocomplete="off" />


                @if ($selectedProdiId || $selectedProdiName)
                    <button type="button" wire:click="resetProdiFilter" $wire.prodiSearchQuery = ''; open=false"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
                        title="Bersihkan Filter">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            @if (strlen($prodiSearchQuery) >= 0 && count($prodiSearchResults) > 0)
                <div x-show="open" x-cloak
                    class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto">

                    @forelse ($prodiSearchResults as $prodi)
                        <div wire:key="prodi-{{ $prodi['id'] }}"
                            wire:click="selectProdiForFilter({{ $prodi['id'] }})" @click="open = false"
                            class="px-4 py-2 cursor-pointer hover:bg-indigo-50 text-gray-800 transition duration-150">
                            <span class="font-medium">{{ $prodi['prodi'] }}</span>
                            <span class="text-xs text-gray-500">({{ $prodi['fakultas'] }}) - ID:
                                {{ $prodi['id'] }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-2 text-gray-500 italic">Tidak ada prodi ditemukan.</div>
                    @endforelse

                </div>
            @endif

        </div>

        {{-- Tombol Reset Filter Utama --}}
        {{-- <div class="sm:col-span-1 relative">
            <button wire:click="resetFilters"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md whitespace-nowrap">
                <i class="fas fa-sync-alt mr-1"></i> Reset
            </button>
        </div> --}}
    </div>
</div>
