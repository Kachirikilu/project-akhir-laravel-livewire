<div class="relative" x-data="{ open: false }">
    <label for="prodi_search" class="block text-sm font-medium text-gray-700">
        Program Studi <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.academic-cap variant="mini" class="text-{{ $colorRole }}-700" />
        </div>

        <input autocomplete="off" wire:model.live.debounce.300ms="prodi_name_search" type="text"
            @focus="open = true; $event.target.select()" @click.outside="open = false"
            @keydown.escape.window="open = false" @keydown.enter.prevent="open = false" id="prodi_search"
            placeholder="Cari Nama Program Studi"
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        @if ($prodi_id || strlen($prodi_name_search) > 0)
            <button wire:click.prevent="resetProdiInput" type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-{{ $colorRole }}-700 hover:text-red-500 transition duration-150"
                title="Bersihkan Pilihan">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
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
    <div x-show="open && ($wire.prodi_name_search.length > 0 || $wire.prodi_results.length > 0)" x-transition.opacity
        x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @forelse ($prodi_results as $prodi)
            <div wire:key="prodi-{{ $prodi['id'] }}"
                wire:click="selectProdi({{ $prodi['id'] }}, '{{ $prodi['prodi'] }}')" @click="open = false"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-600 group transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 group-hover:text-white leading-tight">
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
