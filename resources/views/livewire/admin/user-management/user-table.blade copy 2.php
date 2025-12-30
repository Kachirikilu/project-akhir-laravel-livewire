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

                    {{-- Nama - Sorting A-Z --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('name')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Nama {!! $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    {{-- NIP/NIM Dinamis --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('identity')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            {{ $filter == '' ? 'NIP/NIM' : ($filter == 'mahasiswa' ? 'NIM' : 'NIP') }}
                            {!! $sortField === 'identity' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    {{-- NITK/NIDN Dinamis --}}
                    @if ($filter != 'mahasiswa')
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('identity2')"
                                class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                                {{ $filter == '' ? 'NITK/NIDN' : ($filter == 'admin' ? 'NITK' : 'NIDN') }}
                                {!! $sortField === 'identity2' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </button>
                        </th>
                    @endif

                    @if ($filter == 'dosen' || $filter == '')
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('identity3')"
                                class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                                NIDK
                                {!! $sortField === 'identity3' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </button>
                        </th>
                    @endif

                    {{-- Email --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('email')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Email {!! $sortField === 'email' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>

                    {{-- Angkatan - Autocomplete Input --}}
                    @if ($filter == 'mahasiswa')
                        <th class="px-6 py-3 text-left">
                            <div class="flex flex-col gap-1 items-center">
                                <button wire:click="sortBy('tahun_angkatan')"
                                    class="flex items-center text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 focus:outline-none">
                                    Angkatan
                                    @if ($sortField === 'tahun_angkatan')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>


                                <div class="sm:col-span-4 relative w-fit">
                                    <div class="relative">
                                        <input wire:model.live.debounce.300ms="searchAngkatan" list="list-angkatan"
                                            type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                            placeholder="Tahun"
                                            class="mt-1 text-[10px] w-13 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm block">

                                        @if ($searchAngkatan)
                                            <button type="button" wire:click="resetInputAngkatan"
                                                class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 hover:text-red-500 transition duration-150"
                                                title="Bersihkan Filter">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- <input wire:model.live.debounce.300ms="searchAngkatan" list="list-angkatan"
                                            type="text" placeholder="Filter..."
                                            class="mt-1 text-[10px] w-24 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm">
                                        <datalist id="list-angkatan">
                                            @foreach ($angkatanList as $tahun)
                                                <option value="{{ $tahun }}">
                                            @endforeach
                                        </datalist> --}}
                                    </div>
                                </div>
                            </div>
                        </th>
                    @endif

                    {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodi</th> --}}
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('prodi')"
                            class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                            Prodi {!! $sortField === 'prodi' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </button>
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>

            {{-- Body Table --}}
            <tbody wire:loading.class="opacity-50"
                wire:target="search, filterBy, selectProdiForFilter, resetProdiFilter, resetInputFilter, searchAngkatan, resetInputAngkatan, sortBy, perPage, gotoPage, previousPage, nextPage"
                class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    @php
                        $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
                    @endphp

                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name ?? '-' }}</td>
                        {{-- @if ($filter == 'dosen' || $filter == 'mahasiswa') --}}
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $user->identity ?? '-' }}
                        </td>
                        {{-- @endif --}}
                        @if ($filter != 'mahasiswa')
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $user->identity2 ?? '-' }}
                            </td>
                        @endif
                        @if ($filter == 'dosen' || $filter == '')
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $user->identity3 ?? '-' }}
                            </td>
                        @endif
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                        @if ($filter == 'mahasiswa')
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $detail->tahun_angkatan ?? '-' }}</td>
                        @endif
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $detail->prodi->prodi ?? '-' }}
                        </td>

                        {{-- Role --}}
                        <td class="px-6 py-4 text-center text-sm">
                            @switch($user->role)
                                @case('Admin')
                                    <flux:badge icon="cog-6-tooth" color="red" size="sm">Admin</flux:badge>
                                @break

                                @case('Dosen')
                                    <flux:badge icon="briefcase" color="lime" size="sm">Dosen</flux:badge>
                                @break

                                @case('Mahasiswa')
                                    <flux:badge icon="book-open" color="cyan" size="sm">Mahasiswa</flux:badge>
                                @break

                                @default
                                    <flux:badge icon="user-circle" size="sm">{{ $user->role }}</flux:badge>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user->status ?? '-' }}

                        <td class="px-6 py-4 text-center text-sm space-x-2 gap-2">
                            <div class="flex justify-center">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                        inset="top bottom"></flux:button>

                                    <flux:menu>
                                        @if (Auth::user()?->admin)
                                            <flux:menu.item wire:click="editUser({{ $user->id }})"
                                                class="!text-yellow-600 hover:!bg-yellow-100">
                                                <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

                                                <div class="flex justify-between items-center w-full">
                                                    <span>Edit Data</span>
                                                    <flux:icon wire:loading wire:target="editUser({{ $user->id }})"
                                                        name="arrow-path" class="animate-spin h-4 w-4" />
                                                </div>
                                            </flux:menu.item>


                                            @if (Auth::id() != $user->id)
                                                <flux:menu.separator />
                                                <flux:menu.item wire:click="confirmDelete({{ $user->id }})"
                                                    class="!text-red-800 hover:!bg-red-50">
                                                    <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                                                    <div class="flex justify-between items-center w-full">
                                                        <span>Hapus Pengguna</span>
                                                        <flux:icon wire:loading
                                                            wire:target="confirmDelete({{ $user->id }})"
                                                            name="arrow-path" class="animate-spin h-4 w-4" />
                                                    </div>
                                                </flux:menu.item>
                                            @endif
                                        @endif
                                        {{-- <flux:menu.item icon="eye">Lihat Detail</flux:menu.item>
                                            @endif --}}
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </td>
                    </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="p-4" id="pagination-links-container" wire:loading.remove
                    wire:target="gotoPage, previousPage, nextPage">
                    {{ $users->links() }}
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
