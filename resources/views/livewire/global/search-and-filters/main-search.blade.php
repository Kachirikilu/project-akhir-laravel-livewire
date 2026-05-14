<div x-data="{ search: @entangle('search'){{ $isLive ?? false ? '.live' : '' }} }" class="relative flex items-center">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini" class="text-[var(--contrast-second-text)]" />
    </div>

    <input @if ($isLive ?? false) x-model.debounce.{{ $isBounce ?? '300ms' }}="search" @else x-model="search" @endif type="text"
        name="search" placeholder="{{ $placeholder ?? 'Cari data...' }}"
        class="w-full h-10 pl-10 {{ !($isLive ?? false) ? 'pr-26' : 'pr-10' }} rounded-lg shadow-sm
               bg-[var(--second-table-color)] border-{{ ($isBorder ?? 0) ? $isBorder : 0  }} border-[var(--border-table-color)] text-[var(--contrast-main-text)]" />

    <div class="absolute inset-y-0 right-0 flex items-center pr-1 ">
        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search.length > 0',
            'xClick' => "search = ''",
            'xWire' => 'resetInputFilter()',
            'isRelative' => true,
        ])

        @if (!($isLive ?? false))
            <button type="button" @click="$wire.$set('search', search); $wire.search()" wire:loading.attr="disabled"
                class="cursor-pointer h-8 px-5 bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--focus-color)] text-white rounded-md flex items-center shadow-sm transition-all duration-200 ease-in-out">
                <flux:icon.magnifying-glass class="h-4 w-4" variant="mini" wire:loading.remove wire:target="search" />
                <flux:icon name="arrow-path" class="animate-spin h-4 w-4" wire:loading wire:target="search" />
            </button>
        @endif
    </div>
</div>