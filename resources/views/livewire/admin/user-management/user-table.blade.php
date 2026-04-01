<x-global.main-layout-table>

    @php
        $padingKolom = 'px-6 py-4 text-sm';
        $headKolom =
            'bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] ' .
            $padingKolom;

        $mainKolom =
            'bg-[var(--main-table-trans)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]' .
            ' border-x ' .
            $padingKolom;
        $secondKolom = 'bg-[var(--second-table-trans)] text-[var(--contrast-second-text)] ' . $padingKolom;

        $headSubKolom =
            'bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--focus-color)] border-x border-b text-center font-bold uppercase ' .
            $padingKolom;
        $subKolom =
            'bg-[var(--sub-table-trans)] border-[var(--border-table-color)] text-[var(--contrast-second-text)] ' .
            $padingKolom;
    @endphp

    <x-slot:header>
        <tr>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @if ($filterUser == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'role',
                    'rowSpan' => 2,
                ])
            @else
                <th rowspan="2" class="{{ $headKolom }} . ' uppercase'">Role</th>
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'email',
                'rowSpan' => 2,
            ])

            <th colspan="{{ $filterUser == 'mahasiswa' ? 1 : ($filterUser == 'admin' ? 2 : 3) }}" class="{{ $headSubKolom }}">
                Identitas (ID)
            </th>

            {{-- Angkatan - Autocomplete Input --}}
            @if ($filterUser == 'mahasiswa')
                <th rowspan="2" class="{{ $headKolom }} text-center relative">
                    <div class="flex flex-col gap-1 items-center">

                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'tahun_angkatan',
                            'headString' => 'Angkatan',
                            'withTh' => 0,
                        ])

                        <div x-data="{ value: @entangle('searchAngkatan') }" class="sm:col-span-4 relative w-fit">
                            <div class="relative">

                                <input x-model="value" wire:model.live.debounce.300ms="searchAngkatan"
                                    list="list-angkatan" type="text" inputmode="numeric" pattern="[0-9]*"
                                    maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)"
                                    placeholder="Tahun"
                                    class="mt-1 text-[10px] w-13 border-gray-300 dark:border-neutral-700 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm block">

                                {{-- Tombol Reset --}}
                                @include('livewire.global.search-and-filters.partial.reset-button', [
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

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'prodi',
                'headString' => 'Program Studi',
                'rowSpan' => 2
            ])
            <th rowspan="2" class="{{ $headKolom }} . ' border-x uppercase'">Aksi</th>

            @include('livewire.global.table.head-table', ['sortFieldString' => 'created_at', 'headString' => 'Created At', 'rowSpan' => 2])
            @include('livewire.global.table.head-table', ['sortFieldString' => 'updated_at', 'headString' => 'Updated At', 'rowSpan' => 2])

        </tr>

        {{-- NIP/NIM
        NITK/NIDN
        NIDK --}}

        {{-- $filterUser == '' ? 'NIP/NIM' : ($filterUser == 'mahasiswa' ? 'NIM' : 'NIP'), --}}

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'identity1',
                'headString' => $filterUser == '' ? 'NIP/NIM' : ($filterUser == 'mahasiswa' ? 'NIM' : 'NIP'),
                // 'isSubHeader' => 1,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($filterUser !== 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'identity2',
                    'headString' => $filterUser == '' ? 'NITK/NIDN' : ($filterUser == 'dosen' ? 'NIDN' : 'NIDK'),
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $filterUser == 'admin' ? 1 : 0,
                ])
                @if ($filterUser !== 'admin')
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'identity3',
                        'headString' => 'NIDK',
                        // 'isSubHeader' => 1,
                        'isCenter' => 1,
                        'isBorderR' => 1,
                    ])
                @endif
            @endif
        </tr>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $mainKolom }} text-center">
                {{ $user->id }}</td>
            {{-- Role --}}
            <td class="{{ $secondKolom }} text-center">
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

                    @include('livewire.admin.user-management.modal-form.user-menu', [
                        'x' => $user,
                        'nameXString' => 'Pengguna'
                    ])

                </flux:dropdown>
            </td>
            <td class="{{ $mainKolom }} min-w-84">{{ $user->name ?? '-' }}</td>
            <td class="{{ $secondKolom }}">{{ $user->email }}</td>
            <td class="{{ $mainKolom }} text-center">{{ $user->identity1 ?? '-' }}</td>
            @if ($filterUser != 'mahasiswa')
                <td class="{{ $subKolom }} {{ $filterUser == 'admin' ? 'border-r' : '' }} text-center">
                    {{ $user->identity2 ?? '-' }}
                </td>
            @endif
            @if ($filterUser == 'dosen' || $filterUser == '')
                <td
                    class="{{ $subKolom }} {{ $filterUser == '' || $filterUser == 'dosen' ? 'border-r' : '' }} text-center">
                    {{ $user->identity3 ?? '-' }}
                </td>
            @endif
            @if ($filterUser == 'mahasiswa')
                <td class="{{ $secondKolom }} text-center">{{ $detail->tahun_angkatan ?? '-' }}</td>
            @endif

            <td class="{{ $secondKolom }} text-center">
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

                    @include('livewire.admin.user-management.modal-form.user-menu', [
                        'x' => $user,
                        'nameXString' => 'Pengguna'
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} min-w-48">
                {{ $detail->prodi->prodi ?? '-' }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.user-management.modal-form.user-menu', [
                        'x' => $user,
                        'nameXString' => 'Pengguna'
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} min-w-48">{{ $user->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} min-w-48">{{ $user->updated_day ?? '-' }}</td>
        </tr>

        @empty
            <tr>
                <td colspan="{{ match ($filterUser) {
                    'admin' => 11,
                    'dosen' => 12,
                    'mahasiswa' => 11,
                    default => 12,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data Pengguna ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $users,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
