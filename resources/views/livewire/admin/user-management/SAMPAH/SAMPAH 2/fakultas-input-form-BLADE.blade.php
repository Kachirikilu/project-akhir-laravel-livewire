<div class="relative" x-data="{ open: false }">
    <label for="fakultas_search" class="block text-sm font-medium text-gray-700">
        Fakultas <span class="text-red-500">*</span>
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.book-open variant="mini" class="{{ $colorIcon }}" />
        </div>

        <input autocomplete="off" wire:model.live.debounce.300ms="fakultas_name_search" type="text"
            @focus="open = true; $event.target.select()" @click.outside="open = false"
            @keydown.escape.window="open = false" @keydown.enter.prevent="open = false" id="fakultas_search"
            placeholder="Cari nama Fakultas..."
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">

        {{-- Tombol Reset --}}
        @if ($fakultas_id || strlen($fakultas_name_search) > 0)
            <button wire:click.prevent="resetFakultasInput" type="button"
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

    {{-- INFO FAKULTAS TERPILIH --}}
    @if ($fakultas_id && $fakultas_name_search)
        <p class="text-xs text-indigo-600 mt-1 font-medium italic">
            Terpilih: {{ $fakultas_name_search }} (ID: {{ $fakultas_id }})
        </p>
    @endif

    {{-- FLOATING RESULTS --}}
    <div x-show="open && ($wire.fakultas_name_search.length > 0 || $wire.fakultas_results.length > 0)" x-transition.opacity
        x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @forelse ($fakultas_results as $fakultas)
            <div wire:key="fakultas-{{ $fakultas['id'] }}"
                wire:click="selectFakultas({{ $fakultas['id'] }}, '{{ $fakultas['fakultas'] }}')" @click="open = false"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800 leading-tight">
                            Fakultas {{ $fakultas['fakultas'] }}
                        </span>
                        {{-- <span class="text-xs text-gray-500 mt-0.5">
                            Fakultas {{ $fakultas['fakultas'] }}
                        </span> --}}
                    </div>
                    <span
                        class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md {{ $colorIcon }} ml-2">
                        ID: {{ $fakultas['id'] }}
                    </span>
                </div>
            </div>
        @empty
            @if (strlen($fakultas_name_search) > 0 && !$fakultas_id)
                <div class="p-4 text-center">
                    <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                </div>
            @endif
        @endforelse
    </div>

    {{-- ERROR MESSAGES --}}
    @error('fakultas_id')
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
