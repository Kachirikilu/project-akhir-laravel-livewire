<x-admin.global.table.main-layout-table>

    @php
        $padingKolom = 'px-6 py-4 text-sm';
        $headKolom =
            'bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] ' .
            $padingKolom;

        $mainKolom = 'bg-[var(--main-table-trans)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]' . ' border-x ' . $padingKolom;
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
            <th class="{{ $headKolom }} . ' uppercase'">Aksi</th>

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="border-[var(--border-table-color)] hover:bg-blue-200/60 dark:hover:bg-gray-700/60 transition-colors duration-200">
            
            <td class="{{ $secondKolom }} text-center">{{ $x->id }}</td>

            <td
                class="{{ $mainKolom }} text-center">
                {{ $x->kode_text ?? ($x->kode ?? '-') }}</td>

            @if ($switchTable === 'prodi')
                <td class="{{ $secondKolom }}">{{ $x->prodi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                <td class="{{ $secondKolom }}">
                    {{ $switchTable === 'jurusan' ? 'Jurusan ' : '' }}{{ $x->jurusan ?? '-' }}</td>
            @endif

            <td class="{{ $secondKolom }}">
                {{ $switchTable === 'fakultas' ? 'Fakultas ' : '' }}{{ $x->fakultas ?? '-' }}</td>

            @if ($switchTable === 'prodi')
                <td
                    class="{{ $mainKolom }} text-center">
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
                <td colspan="{{ match ($filter) {
                    'prodi' => 7,
                    'jurusan' => 5,
                    'fakultas' => 4,
                    default => 7,
                } }}" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeXString' => $xResults,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
