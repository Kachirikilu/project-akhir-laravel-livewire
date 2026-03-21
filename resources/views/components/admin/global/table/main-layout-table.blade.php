<div class="bg-white dark:bg-neutral-800 shadow-lg rounded-lg overflow-hidden transition-colors duration-300" id="table-results-container">

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Head Table --}}
            <thead class="bg-gray-50 dark:bg-neutral-700/50 border-gray-400 dark:border-neutral-500">
                    {{ $header }}
                    {{-- Body Table --}}
            <tbody 
            {{-- wire:loading.class="opacity-50"  --}}
                wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                wire:target="prodiSearchQuery, jurusanSearchQuery, fakultasSearchQuery,
                filterBy,
                saveAllRows, processImport, saveUserInternal,
                saveUser, updateUser, destroyUser, saveProdi, updateProdi, destroyProdi,
                search, selectProdiForFilter, resetProdiFilter, selectFakultasForFilter, resetFakultasFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
                class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700 transition-colors duration-300">
                {{ $slot }}
            </tbody>
        </table>


    </div>
    {{-- FOOTER --}}
    {{ $footer }}
</div>
