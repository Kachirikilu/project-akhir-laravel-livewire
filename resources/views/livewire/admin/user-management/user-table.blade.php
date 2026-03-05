<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
            @if ($filter == '')
                <button wire:click="sortBy('role')"
                    class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap">
                    Role {!! $sortField === 'role' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                </button>
            @else
                Role
            @endif
        </th>

        {{-- Name - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'name'])

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



        {{-- Email - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'email'])


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
                            <input wire:model.live.debounce.300ms="searchAngkatan" list="list-angkatan" type="text"
                                inputmode="numeric" pattern="[0-9]*" maxlength="4"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)" placeholder="Tahun"
                                class="mt-1 text-[10px] w-13 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm block">

                            @if ($searchAngkatan)
                                <button type="button" wire:click="resetInputAngkatan"
                                    class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 hover:text-red-500 transition duration-150"
                                    title="Bersihkan Filter">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            </th>
        @endif

        {{-- Prodi - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'prodi'])



        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
            {{-- Role --}}
            <td class="px-6 py-4 text-center text-sm">
                <flux:dropdown>

                    <button>
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
                    </button>

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
                                        <flux:icon wire:loading wire:target="confirmDelete({{ $user->id }})"
                                            name="arrow-path" class="animate-spin h-4 w-4" />
                                    </div>
                                </flux:menu.item>
                            @endif
                        @endif

                    </flux:menu>
                </flux:dropdown>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">
                {{ $user->identity ?? '-' }}
            </td>
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

            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->status ?? '-' }}

                @include('livewire.admin.global.table.menu-aksi', [
                    'typeOfXString' => $user,
                    'nameXString' => 'Pengguna',
                ])
        </tr>

        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada pengguna ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeOfXString' => $users,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
