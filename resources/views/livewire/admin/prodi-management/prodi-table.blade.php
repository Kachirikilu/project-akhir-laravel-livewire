<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id', 'isCenter' => 1])

        @if ($switchTable === 'prodi')
            {{-- Prodi - Sorting A-Z --}}
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'prodi'])
        @endif

        @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
            {{-- Jurusan - Sorting A-Z --}}
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'jurusan'])
        @endif

        {{-- Fakultas - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])

        @if ($switchTable === 'prodi')
            {{-- Strata - Sorting A-Z --}}
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'strata', 'isCenter' => 1])
        @endif
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" class="hover:bg-gray-50"
            data-{{ $switchTable }}-id="{{ $x->id }}">
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ $x->id }}</td>

            @if ($switchTable === 'prodi')
                <td class="px-6 py-4 text-sm text-gray-700">{{ $x->prodi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                <td class="px-6 py-4 text-sm text-gray-700">{{ $x->jurusan ?? '-' }}</td>
            @endif

            <td class="px-6 py-4 text-sm text-gray-700">{{ $x->fakultas ?? '-' }}</td>

            @if ($switchTable === 'prodi')
                <td class="px-6 py-4 text-center text-sm text-gray-700">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($x->strata)
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
                                    <flux:badge icon="academic-cap" size="sm">{{ $x->strata }}</flux:badge>
                            @endswitch
                        </button>

                        @include('livewire.admin.global.table.partial.pop-up-menu', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'editString' => 'editProdi',
                            'nameXString' => $xNameString,
                            'confirmDeleteString' => 'deleteProdi',
                        ])
                    </flux:dropdown>
                </td>
            @endif

            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $x,
                'typeXString' => $switchTable,
                'editString' => 'editProdi',
                'nameXString' => $xNameString,
                'confirmDeleteString' => 'deleteProdi',
            ])
        </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeOfXString' => $xResults,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
