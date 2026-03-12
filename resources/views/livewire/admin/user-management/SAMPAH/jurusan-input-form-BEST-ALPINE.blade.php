<div class="relative" x-data="{
    open: false,
    search: @entangle('jurusan_name_search').live,
    selectedId: @entangle('jurusan_id').live
}">
    <label for="jurusan_search" class="block text-sm font-medium text-gray-700">
        Jurusan <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.book-open variant="mini" class="{{ $colorIcon }}" />
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @focus="
        open = true; 
        $wire.loadDefaultJurusan(search);
        $event.target.select();
    "
            @input="open = true" @click.outside="open = false" @keydown.escape.window="open = false" id="jurusan_search"
            placeholder="Cari nama Jurusan..."
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset Instan --}}
        <button x-show="search || selectedId" x-transition.opacity type="button"
            @click="search = ''; selectedId = null; $wire.resetJurusanInput()"
            class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $colorIcon }} hover:text-red-500 transition duration-150">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Info Terpilih --}}
    <div x-show="selectedId && search" x-cloak>
        <p class="text-xs text-indigo-600 mt-1 font-medium italic">
            Terpilih: <span x-text="search"></span> (ID: <span x-text="selectedId"></span>)
        </p>
    </div>

    {{-- FLOATING RESULTS --}}
    {{-- Syarat buka: 'open' bernilai true. Data diambil dari $jurusan_results di backend --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @forelse ($jurusan_results as $jurusan)
            <div wire:key="jurusan-{{ $jurusan['id'] }}"
                @click="search = 'Jurusan {{ $jurusan['jurusan'] }}'; selectedId = {{ $jurusan['id'] }}; open = false; $wire.selectJurusan({{ $jurusan['id'] }}, '{{ $jurusan['jurusan'] }}')"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 leading-tight">
                            Jurusan {{ $jurusan['jurusan'] }}
                        </span>
                        <span class="text-xs text-gray-500 mt-0.5">
                            Fakultas {{ $jurusan['fakultas'] }}
                        </span>
                    </div>
                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">
                        ID: {{ $jurusan['id'] }}
                    </span>
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
            </div>
        @endforelse
    </div>
</div>
