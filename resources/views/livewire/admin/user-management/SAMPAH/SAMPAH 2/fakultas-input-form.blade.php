<div class="relative" x-data="{
    open: false,
    {{-- Hapus .live agar tidak terjadi sinkronisasi otomatis yang bikin flicker --}}
    search: @entangle('fakultas_name_search'),
    selectedId: @entangle('fakultas_id').live
}">
    <label for="fakultas_search" class="block text-sm font-medium text-gray-700">
        Fakultas <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.book-open variant="mini" class="{{ $colorIcon }}" />
        </div>

        <input x-model="search" autocomplete="off" type="text" {{-- SAAT FOKUS: Buka dropdown & muat data default --}}
            @focus="
                open = true; 
                $event.target.select();
                $wire.fetchFakultas(); 
            "
            {{-- SAAT MENGETIK: Jalankan pencarian dengan debounce agar ringan --}}
            @input.debounce.300ms="
                open = true;
                $wire.fetchFakultas(search); 
            "
            @click.outside="open = false" @keydown.escape.window="open = false" id="fakultas_search"
            placeholder="Cari nama Fakultas..."
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        <button x-show="search || selectedId" type="button" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-50"
            @click="search = ''; selectedId = null; $wire.resetFakultasInput(); $wire.fetchFakultas()"
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

    {{-- DROPDOWN HASIL --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto">

        @forelse ($fakultas_results as $fakultas)
            <div wire:key="fakultas-{{ $fakultas['id'] }}"
                @click="
                    search = 'Fakultas {{ $fakultas['fakultas'] }}'; 
                    selectedId = {{ $fakultas['id'] }}; 
                    open = false; 
                    $wire.selectFakultas({{ $fakultas['id'] }}, '{{ $fakultas['fakultas'] }}')
                "
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 leading-tight">Fakultas {{ $fakultas['fakultas'] }}</span>
                        <span class="text-xs text-gray-500 mt-0.5">Fakultas {{ $fakultas['fakultas'] }}</span>
                    </div>
                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">ID:
                        {{ $fakultas['id'] }}</span>
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
            </div>
        @endforelse
    </div>
</div>