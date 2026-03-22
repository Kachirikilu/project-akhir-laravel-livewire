<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id', 'isMain' => 1, 'isCenter' => 1])

        {{-- Role - Sorting A-Z --}}
        @if ($filter == '')
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'role'])
        @else
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Role</th>
        @endif

        {{-- Name - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'name', 'isMain' => 1])

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

                    <div x-data="{ value: @entangle('searchAngkatan') }" class="sm:col-span-4 relative w-fit">
                        <div class="relative">

                            <input x-model="value" wire:model.live.debounce.300ms="searchAngkatan" list="list-angkatan"
                                type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)" placeholder="Tahun"
                                class="mt-1 text-[10px] w-13 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm block">

                            {{-- Tombol Reset --}}
                            @include('livewire.admin.global.search-and-filters.partial.reset-button', [
                                'xShow' => 'value',
                                'xClick' => "value = ''",
                                'xWire' => 'resetInputAngkatan()',
                                'xSize' => 3,
                                'xPr' => 1,
                            ])

                        </div>
                    </div>

                </div>
            </th>
        @endif

        {{-- Prodi - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'prodi', 'isMain' => 1])
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'status', 'isCenter' => 1])
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
        
            @include('livewire.admin.global.table.body-table', [
                'xData' =>  $user->id ?? '-',
                'sortFieldString' => 'id',
                'isMain' => 1,
                'isCenter' => 1
            ])
            {{-- Role --}}
            <td class="px-6 py-4 text-sm text-gray-700">
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
                        'confirmDeleteString' => 'deleteUser',
                    ])

                </flux:dropdown>
            </td>
            {{-- <td class="px-6 py-4 border-l border-r border-gray-300 bg-gray-50/30 text-sm text-gray-700">{{ $user->name ?? '-' }}</td> --}}
            @include('livewire.admin.global.table.body-table', [
                'xData' =>  $user->name ?? '-',
                'sortFieldString' => 'name',
                'isMain' => 1
            ])

            @include('livewire.admin.global.table.body-table', ['xData' =>  $user->identity1 ?? '-', 'sortFieldString' => 'identity1'])
            @if ($filter != 'mahasiswa')
                @include('livewire.admin.global.table.body-table', ['xData' =>  $user->identity2 ?? '-', 'sortFieldString' => 'identity2'])
            @endif
            @if ($filter == 'dosen' || $filter == '')
                @include('livewire.admin.global.table.body-table', ['xData' =>  $user->identity3 ?? '-', 'sortFieldString' => 'identity3'])
            @endif

            @include('livewire.admin.global.table.body-table', ['xData' =>  $user->email ?? '-', 'sortFieldString' => 'email'])

            @if ($filter == 'mahasiswa')
                @include('livewire.admin.global.table.body-table', ['xData' => $detail->tahun_angkatan ?? '-', 'sortFieldString' => 'tahun_angkatan'])
            @endif
          
            @include('livewire.admin.global.table.body-table', [
                'xData' => $detail->prodi->prodi ?? '-',
                'sortFieldString' => 'prodi',
                'isMain' => 1
            ])


            {{-- <td class="px-6 py-4 text-center text-sm text-gray-700"> --}}
            @include('livewire.admin.global.table.body-table', [
                'sortFieldString' => 'status',
                'isCenter' => 1,
                'isOnlyHeadTd' => 1
            ])
                <flux:dropdown>

                    <button class="cursor-pointer">
                        @switch($user->status)

                            {{-- HIJAU: Status Lulus --}}
                            @case('Lulus')
                                <flux:badge color="blue" size="sm">{{ $user->status }}</flux:badge>
                            @break
                            {{-- HIJAU: Status Aktif --}}
                            @case('Aktif')
                                <flux:badge color="green" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- KUNING: Status Transisi/Sementara --}}
                            @case('Tugas Belajar')
                            @case('Izin Belajar')

                            @case('Mutasi')
                            @case('Cuti')

                            @case('Cuti Sabatika')
                            @case('Cuti Luar Tanggungan')

                            @case('Pindah')
                                <flux:badge color="yellow" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- ORANGE: Keluar Prosedural / Masalah Administrasi --}}
                            @case('Resign')
                            @case('Pensiun')

                            @case('Alih Tugas')
                            @case('Mengundurkan Diri')

                            @case('Non-Aktif')
                                <flux:badge color="orange" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- MERAH: Berhenti Permanen / Sanksi / Masalah Berat --}}
                            @case('Diberhentikan')
                            @case('Drop Out')

                            @case('Meninggal Dunia')
                            @case('Hilang')
                                <flux:badge color="red" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            @default
                                <flux:badge size="sm">{{ $user->status }}</flux:badge>
                        @endswitch
                    </button>
                    
                    @include('livewire.admin.global.table.partial.pop-up-menu', [
                        'x' => $user,
                        'nameXString' => 'Pengguna',
                        'editString' => 'editUser',
                        'confirmDeleteString' => 'deleteUser',
                    ])

                </flux:dropdown>
            </td>

            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $user,
                'nameXString' => 'Pengguna',
                'editString' => 'editUser',
                'confirmDeleteString' => 'deleteUser',
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
                'typeXString' => $users,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
