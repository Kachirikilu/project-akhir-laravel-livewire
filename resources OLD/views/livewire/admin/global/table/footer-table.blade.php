<div>
    {{-- Pagination --}}
    @if ($typeXString->hasPages())
        <div class="p-4" id="pagination-links-container" wire:loading.remove
            wire:target="gotoPage, previousPage, nextPage">
            {{ $typeXString->links() }}
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading.flex
        wire:target="search, filterBy, selectFakultasForFilter, resetFakultasFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
        class="justify-center items-center py-4">
        <div class="flex items-center space-x-2 text-gray-500">
            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Memuat data...</span>
        </div>
    </div>
</div>
