<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])

        {{-- Role - Sorting A-Z --}}
        @if ($filter == '')
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'role'])
        @else
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
        @endif

        {{-- Name - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'name'])

        {{-- NIP/NIM Dinamis --}}
        @include('livewire.admin.global.table.head-table', [
            'sortFieldString' => 'identity1',
            'headString' => $filter == '' ? 'NIP/NIM' : ($filter == 'mahasiswa' ? 'NIM' : 'NIP'),
        ])

        {{-- NITK/NIDN Dinamis --}}
        @if ($filter != 'mahasiswa')
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'identity2',
                'headString' => $filter == '' ? 'NITK/NIDN' : ($filter == 'admin' ? 'NITK' : 'NIDN'),
            ])
        @endif

        @if ($filter == 'dosen' || $filter == '')
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'identity3',
                'headString' => 'NIDK',
            ])
        @endif

        {{-- Email - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'email'])


        {{-- Angkatan - Autocomplete Input --}}
        @if ($filter == 'mahasiswa')
            <th class="px-6 py-3 text-left">
                <div class="flex flex-col gap-1 items-center">
                    @include('livewire.admin.global.table.head-table', [
                        'sortFieldString' => 'tahun_angkatan',
                        'headString' => 'Angkatan',
                        'withTh' => 0,
                    ])

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

                    <button class="cursor-pointer">
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

                    @include('livewire.admin.global.table.partial.pop-up-menu', [
                        'x' => $user,
                        'nameXString' => 'Pengguna',
                        'editString' => 'editUser',
                        'confirmDeleteString' => 'deleteUser'
                    ])

                </flux:dropdown>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->name ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">
                {{ $user->identity1 ?? '-' }}
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
                    'x' => $user,
                    'nameXString' => 'Pengguna',
                    'editString' => 'editUser',
                    'confirmDeleteString' => 'deleteUser'
                ])
        </tr>

        @empty
            <tr>
                <td colspan="{{ match ($filter) {
                    'admin' => 9,
                    'dosen' => 10,
                    'mahasiswa' => 9,
                    default => 10,
                } }}"
                    class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data Pengguna ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeOfXString' => $users,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
