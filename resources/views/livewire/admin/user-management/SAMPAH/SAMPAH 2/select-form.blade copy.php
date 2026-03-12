<div class="relative" x-data="{ open:false, selected:@entangle($modelString).live }"">

    <label for="{{ $modelString }}" class="block text-sm font-medium text-gray-700">
        {{ $labelString }}
        @if ($isRequired ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative mt-2">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon.book-open variant="mini" class="{{ $colorIcon }}" />
        </div>

        {{-- <input
            autocomplete="off"
            wire:model.lazy="{{ $modelString }}"
            type="text"
            readonly
            @focus="open = true"
            @click="open = true"
            @click.outside="open = false"
            @keydown.escape.window="open = false"
            id="{{ $modelString }}"
            placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10 cursor-pointer"
        > --}}

        <input type="text" placeholder="Pilih {{ $labelString }}..." readonly
            value="{{ data_get($this, $modelString) }}" @focus="open = true" @click="open = true"
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10 cursor-pointer">

        {{-- Reset --}}
        @if (data_get($this, $modelString))
            <button type="button" wire:click="$set('{{ $modelString }}', '')"
                class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $colorIcon }} hover:text-red-500 transition duration-150"
                title="Bersihkan Pilihan">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        @endif
    </div>

    {{-- Dropdown Result --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @foreach (['Sarjana', 'Magister', 'Doktor'] as $option)
            <div wire:key="option-{{ $option }}" wire:click="$set('{{ $modelString }}','{{ $option }}')"
                @click="open = false"
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-800 leading-tight">
                        {{ $option }}
                    </span>

                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md {{ $colorIcon }} ml-2">
                        Pilih
                    </span>
                </div>
            </div>
        @endforeach

    </div>

    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror

</div>
