<div class="relative" x-data="{
    open: false,
    value: @entangle($modelString)
}">

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

        <input autocomplete="off" x-model="value" {{-- Gunakan x-model agar Alpine tahu isinya --}} type="text" readonly @click="open = true"
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
            placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
            class="w-full border rounded-lg pl-10 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10 cursor-pointer">

        {{-- 2. Gunakan x-show untuk tampilan instan tanpa delay server --}}
        <button x-show="value" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-50" type="button" @click="value = ''"
            class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 {{ $colorIcon }} hover:text-red-500 transition duration-150"
            title="Bersihkan Pilihan">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Dropdown Result --}}
    <div x-show="open" x-transition.opacity x-cloak
        class="absolute left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden">

        @foreach (['Sarjana', 'Magister', 'Doktor'] as $option)
            <div wire:key="option-{{ $option }}" @click="value = '{{ $option }}'; open = false"
                {{-- Set nilai via Alpine --}}
                class="px-4 py-3 cursor-pointer hover:bg-indigo-50 transition duration-150 border-b border-gray-50 last:border-none">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-800 leading-tight">{{ $option }}</span>
                    <span class="text-[10px] bg-indigo-500 text-white px-2 py-1 rounded-md ml-2">Pilih</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
