<div class="bg-white shadow-lg rounded-lg overflow-hidden" id="user-results-container">

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Head Table --}}
            <thead class="bg-gray-50">
                <tr class="bg-gray-50">
                    {{ $header }}
                </tr>
            </thead>

            {{-- Body Table --}}
            <tbody wire:loading.class="opacity-50"
                wire:target="search, filterBy, selectFakultasForFilter, resetFakultasFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
                class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>

        {{-- FOOTER --}}
        {{ $footer }}

    </div>
</div>
