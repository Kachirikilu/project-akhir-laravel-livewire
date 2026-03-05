<div class="bg-white shadow-lg rounded-lg overflow-hidden" id="user-results-container">

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">

            {{-- Head Table --}}
            <thead class="bg-gray-50">

                <tr class="bg-gray-50">
                    {{-- ID - Sorting Angka --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('id')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            ID {!! $sortField === 'id' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    {{-- Fakultas - Sorting A-Z --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('fakultas')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Fakultas {!! $sortField === 'fakultas' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>

            {{-- Body Table --}}
            <tbody wire:loading.class="opacity-50"
                wire:target="search, filterBy, selectFakultasForFilter, resetFakultasFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
                class="bg-white divide-y divide-gray-200">
                @forelse($fakultass as $fakultas)
                    <tr wire:key="fakultas-{{ $fakultas->id }}" class="hover:bg-gray-50"
                        data-fakultas-id="{{ $fakultas->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $fakultas->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Fakultas {{ $fakultas->fakultas ?? '-' }}</td>

                        <td class="px-6 py-4 text-center text-sm space-x-2 gap-2">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom"></flux:button>

                                <flux:menu>
                                    @if (Auth::user()?->admin)
                                        <flux:menu.item wire:click="editUser({{ $fakultas->id }})"
                                            class="!text-yellow-600 hover:!bg-yellow-100">
                                            <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

                                            <div class="flex justify-between items-center w-full">
                                                <span>Edit Data</span>
                                                <flux:icon wire:loading wire:target="editUser({{ $fakultas->id }})"
                                                    name="arrow-path" class="animate-spin h-4 w-4" />
                                            </div>
                                        </flux:menu.item>

                                        <flux:menu.separator />
                                        <flux:menu.item wire:click="confirmDelete({{ $fakultas->id }})"
                                            class="!text-red-800 hover:!bg-red-50">
                                            <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                                            <div class="flex justify-between items-center w-full">
                                                <span>Hapus Fakultas</span>
                                                <flux:icon wire:loading wire:target="confirmDelete({{ $fakultas->id }})"
                                                    name="arrow-path" class="animate-spin h-4 w-4" />
                                            </div>
                                        </flux:menu.item>
                                    @endif


                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada fakultas ditemukan!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        {{-- Pagination --}}
        @if ($fakultass->hasPages())
            <div class="p-4" id="pagination-links-container" wire:loading.remove
                wire:target="gotoPage, previousPage, nextPage">
                {{ $fakultass->links() }}
            </div>
        @endif

        {{-- Loading indicator --}}
        <div wire:loading.flex
            wire:target="search, filterBy, selectFakultasForFilter, resetFakultasFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
            class="justify-center items-center py-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

    </div>
</div>
