<div x-data="{ search: @entangle('search').live }" class="relative">
    {{-- Icon Magnifying Glass --}}
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini" class="text-gray-400 dark:text-gray-500" />
    </div>

    {{-- Input Search --}}
    <input x-model.debounce.300ms="search" type="text" name="search" placeholder="{{ $placeholder ?? 'Cari data...' }}"
        class="w-full h-10 pl-10 px-4 rounded-lg shadow-sm transition-all duration-200
               bg-white border-gray-300 text-gray-900 placeholder-gray-400
               dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-100 dark:placeholder-gray-500
               focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400" />

    {{-- Tombol Reset --}}
    @include('livewire.admin.global.search-and-filters.partial.reset-button', [
        'xShow' => 'search.length > 0',
        'xClick' => "search = ''",
        'xWire' => 'resetInputFilter()'
    ])
</div>