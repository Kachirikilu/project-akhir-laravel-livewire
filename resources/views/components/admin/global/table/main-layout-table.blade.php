<div class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] shadow-lg rounded-lg overflow-hidden" id="table-results-container">

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y">
            {{-- Head Table --}}
            <thead
            class="bg-[var(--main-table-color)] border-[var(--border-table-color)]"
            {{-- class="bg-gray-50 dark:bg-neutral-700/50 border-gray-400 dark:border-neutral-500" --}}
            >
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
                class="bg-[var(--second-table-color)] border-[var(--border-table-color)] divide-y">
                {{ $slot }}
            </tbody>
        </table>


    </div>
    {{-- FOOTER --}}
    {{ $footer }}
</div>
