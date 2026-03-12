<div class="relative" x-data="{ open: false }">
    <label for="jurusan_search" class="block text-sm font-medium text-gray-700">
        Jurusan <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.academic-cap variant="mini" class="{{ $colorIcon }}" />
        </div>

        <input autocomplete="off" wire:model.live.debounce.300ms="jurusan_name_search" type="text"
            @focus="open = true; $event.target.select()" @click.outside="open = false"
            @keydown.escape.window="open = false" @keydown.enter.prevent="open = false" id="jurusan_search"
            placeholder="Cari nama Jurusan..."
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        @if ($jurusan_id || strlen($jurusan_name_search) > 0)
            <button wire:click.prevent="resetJurusanInput" type="button"
                class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $colorIcon }} hover:text-red-500 transition duration-150"
                title="Bersihkan Pilihan">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        @endif
    </div>

    {{-- INFO JURUSAN TERPILIH --}}
    @if ($jurusan_id && $jurusan_name_search)
        <p class="text-xs text-indigo-600 mt-1 font-medium italic">
            Terpilih: {{ $jurusan_name_search }} (ID: {{ $jurusan_id }})
        </p>
    @endif

    {{-- FLOATING RESULTS --}}
    <div x-show="open && ($wire.jurusan_name_search.length > 0 || $wire.jurusan_results.length > 0)" x-transition.opacity
        x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @forelse ($jurusan_results as $jurusan)
            <div wire:key="jurusan-{{ $jurusan['id'] }}"
                wire:click="selectJurusan({{ $jurusan['id'] }}, '{{ $jurusan['jurusan'] }}')" @click="open = false"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 leading-tight">
                            {{ $jurusan['jurusan'] }}
                        </span>
                        <span class="text-xs text-gray-500 mt-0.5">
                            Fakultas {{ $jurusan['fakultas'] }}
                        </span>
                    </div>
                    <span
                        class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md {{ $colorIcon }} ml-2">
                        ID: {{ $jurusan['id'] }}
                    </span>
                </div>
            </div>
        @empty
            @if (strlen($jurusan_name_search) > 0 && !$jurusan_id)
                <div class="p-4 text-center">
                    <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                </div>
            @endif
        @endforelse
    </div>

    {{-- ERROR MESSAGES --}}
    @error('jurusan_id')
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
