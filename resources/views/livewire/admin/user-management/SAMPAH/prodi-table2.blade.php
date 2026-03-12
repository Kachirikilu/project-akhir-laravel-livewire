<div class="bg-white shadow-lg rounded-lg overflow-hidden" id="prodi-results-container">

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

                    {{-- Nama - Sorting A-Z --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('prodi')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Prodi {!! $sortField === 'prodi' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    {{-- Email --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('jurusan')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Jurusan {!! $sortField === 'jurusan' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('fakultas')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Fakultas {!! $sortField === 'fakultas' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('strata')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Strata {!! $sortField === 'strata' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>

            {{-- Body Table --}}
            <tbody wire:loading.class="opacity-50"
                wire:target="search, filterBy, selectProdiForFilter, resetProdiFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
                class="bg-white divide-y divide-gray-200">
                @forelse($prodis as $prodi)

                    <tr wire:key="prodi-{{ $prodi->id }}" class="hover:bg-gray-50" data-prodi-id="{{ $prodi->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $prodi->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->prodi ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->jurusan ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->fakultas ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->strata ?? '-' }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada Program Studi ditemukan!
                            </td>
                        </tr>
                    @endforelse
                </tbody>


            {{-- Pagination --}}
            @if ($prodis->hasPages())
                <div class="p-4" id="pagination-links-container" wire:loading.remove
                    wire:target="gotoPage, previousPage, nextPage">
                    {{ $prodis->links() }}
                </div>
            @endif

            {{-- Loading indicator --}}
            <div wire:loading.flex
                wire:target="search, filterBy, selectProdiForFilter, resetProdiFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
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

            </table>
        </div>
    </div>
