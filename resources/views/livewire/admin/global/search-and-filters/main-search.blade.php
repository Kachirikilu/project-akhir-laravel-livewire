<div x-data="{ search: @entangle('search').live }" class="relative">
    {{-- Icon Magnifying Glass --}}
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini" class="text-gray-400" />
    </div>

    {{-- Input Search --}}
    <input x-model.debounce.300ms="search" type="text" name="search" placeholder="{{ $placeholder ?? 'Cari data...' }}"
        class="w-full h-10 pl-10 px-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" />

    {{-- Tombol Reset --}}
    @include('livewire.admin.global.search-and-filters.partial.reset-button', [
        'xShow' => 'search.length > 0',
        'xClick' => "search = ''",
        'xWire' => 'resetInputFilter()'
    ])
</div>
