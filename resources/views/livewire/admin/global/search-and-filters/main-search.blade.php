<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
    <flux:icon.magnifying-glass variant="mini" class="text-gray-400" />
</div>
<input wire:model.live="search" type="text" name="search" value="{{ $search ?? '' }}"
    placeholder="{{ $placeholder ?? 'Cari data...' }}"
    class="w-full h-10 pl-10 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" />

@if ($search)
    <button type="button" wire:click="resetInputFilter" $wire.search = ''; open=false"
        class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition duration-150"
        title="Bersihkan Filter">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
            </path>
        </svg>
    </button>
@endif
