<x-admin.global.table.main-layout-table>

    <x-slot:header>

        <tr class="bg-gray-50 dark:bg-neutral-800/50">


            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
            ])
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isMain' => 1,
            ])

            @if ($switchTable === 'prodi')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'prodi',
                    'headString' => 'Program Studi',
                ])
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'jurusan'])
            @endif

            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])

            @if ($switchTable === 'prodi')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'strata',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors duration-200"
            data-{{ $switchTable }}-id="{{ $x->id }}">
            <td class="px-6 py-4 text-center text-sm font-medium">{{ $x->id }}</td>

            <td
                class="px-6 py-4 border-x border-gray-300 dark:border-neutral-700 bg-gray-100/30 dark:bg-neutral-700/30 text-sm text-gray-700 dark:text-gray-200">
                {{ $x->kode_text ?? ($x->kode ?? '-') }}</td>

            @if ($switchTable === 'prodi')
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $x->prodi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                    {{ $switchTable === 'jurusan' ? 'Jurusan ' : '' }}{{ $x->jurusan ?? '-' }}</td>
            @endif

            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                {{ $switchTable === 'fakultas' ? 'Fakultas ' : '' }}{{ $x->fakultas ?? '-' }}</td>

            @if ($switchTable === 'prodi')
                <td
                    class="px-6 py-4 border-x border-gray-300 dark:border-neutral-700 bg-gray-100/30 dark:bg-neutral-700/30 text-center text-sm text-gray-700 dark:text-gray-200">
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
