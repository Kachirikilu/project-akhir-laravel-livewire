<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])
        {{-- Prodi - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'prodi'])
        {{-- Jurusan - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'jurusan'])
        {{-- Fakultas - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])
        {{-- Strata - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'strata'])
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($prodis as $prodi)
        <tr wire:key="prodi-{{ $prodi->id }}" class="hover:bg-gray-50" data-prodi-id="{{ $prodi->id }}">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $prodi->id }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->prodi ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->jurusan ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->fakultas ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($prodi->strata)
                            @case('Sarjana')
                                <flux:badge icon="academic-cap" color="sky" size="sm">Sarjana</flux:badge>
                            @break

                            @case('Magister')
                                <flux:badge icon="building-library" color="emerald" size="sm">Magister
                                </flux:badge>
                            @break

                            @case('Doktor')
                                <flux:badge icon="light-bulb" color="amber" size="sm">Doktor</flux:badge>
                            @break

                            @default
                                <flux:badge icon="academic-cap" size="sm">{{ $prodi->strata }}</flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.admin.global.table.partial.pop-up-menu', [
                        'x' => $prodi,
                        'nameXString' => 'Program Studi',
                        'editString' => 'editProdi',
                        'confirmDeleteString' => 'confirmDelete',
                    ])
                </flux:dropdown>
            </td>

            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $prodi,
                'nameXString' => 'Program Studi',
                'editString' => 'editProdi',
                'confirmDeleteString' => 'confirmDelete',
            ])
        </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada prodi ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeXString' => $prodis,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
