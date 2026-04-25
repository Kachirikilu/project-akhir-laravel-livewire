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
                'isCenter' => 1
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isMain' => 1,
                'isCenter' => 1
            ])

            @if ($switchTable === 'prodi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'prodi',
                    'headString' => 'Program Studi'
                ])
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                @include('livewire.global.table.head-table', ['sortFieldString' => 'jurusan'])
            @endif

            @include('livewire.global.table.head-table', ['sortFieldString' => 'fakultas'])

            @if ($switchTable === 'prodi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'strata',
                    'isCenter' => 1,
                    'isMain' => 1
                ])
            @endif
            <th class="{{ $headKolom }} . ' border-x uppercase'">Aksi</th>

            @include('livewire.global.table.head-table', ['sortFieldString' => 'created_at', 'headString' => 'Created At', 'isCenter' => 1])
            @include('livewire.global.table.head-table', ['sortFieldString' => 'updated_at', 'headString' => 'Updated At', 'isCenter' => 1])

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="border-[var(--border-table-color)] hover:bg-gray-400 dark:hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $x->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($x->tingkatan_prodi)
                            @case(1)
                                <flux:badge icon="academic-cap" color="emerald" size="sm">
                                    {{ $x->kode ?? '-' }}
                                </flux:badge>
                            @break

                            @case(2)
                                <flux:badge icon="book-open" color="amber" size="sm">
                                    {{ $x->kode ?? '-' }}
                                </flux:badge>
                            @break

                            @case(3)
                                <flux:badge icon="building-library" color="indigo" size="sm">
                                    {{ $x->kode ?? '-' }}
                                </flux:badge>
                            @break

                            @default
                                <flux:badge icon="globe-alt" color="red" size="sm">
                                    {{ $x->kode ?? '-' }}
                                </flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])
                </flux:dropdown>
            </td>

            @if ($switchTable === 'prodi')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->prodi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'prodi' || $switchTable === 'jurusan')
                <td class="{{ $secondKolom }} min-w-48">
                    {{ $switchTable === 'jurusan' ? 'Jurusan ' . $x->jurusan : $x->jurusan . ' (' . $x->kode_jr . ')' }}</td>
            @endif

            <td class="{{ $secondKolom }} min-w-48">
                    {{ $switchTable === 'fakultas' ? 'Fakultas ' . $x->fakultas : $x->fakultas . ' (' . $x->kode_fk . ')' }}</td>


            @if ($switchTable === 'prodi')
                <td class="{{ $mainKolom }} text-center">
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

                        @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} min-w-48 text-center">{{ $x->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} min-w-48 text-center">{{ $x->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($filterPr) {
                    'prodi' => 9,
                    'jurusan' => 7,
                    'fakultas' => 6,
                    default => 9,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $xResults,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
