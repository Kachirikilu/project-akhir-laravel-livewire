<x-admin.global.table.main-layout-table>

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
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isMain' => 1,
            ])

            @if ($switchTable === 'prodi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'prodi',
                    'headString' => 'Program Studi',
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
                    'isMain' => 1,
                ])
            @endif
            <th class="{{ $headKolom }} . ' uppercase'">Aksi</th>

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $x->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button variant="ghost" size="sm" class="cursor-pointer">
                        @switch($x->tingkatanProdi)
                            @case(1)
                                <flux:badge icon="academic-cap" color="emerald" size="sm">
                                    {{ $x->kode_text ?? ($x->kode ?? '-') }}
                                </flux:badge>
                            @break

                            @case(2)
                                <flux:badge icon="book-open" color="amber" size="sm">
                                    {{ $x->kode_text ?? ($x->kode ?? '-') }}
                                </flux:badge>
                            @break

                            @case(3)
                                <flux:badge icon="building-library" color="indigo" size="sm">
                                    {{ $x->kode_text ?? ($x->kode ?? '-') }}
                                </flux:badge>
                            @break

                            @default
                                <flux:badge icon="globe-alt" color="red" size="sm">
                                    {{ $x->kode_text ?? ($x->kode ?? '-') }}
                                </flux:badge>
                        @endswitch
                    </flux:button>

                    {{-- Pastikan di dalam include ini isinya adalah <flux:menu> ... </flux:menu> --}}
                    @include('livewire.admin.prodi-management.modal-form.prodi-menu', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])
                </flux:dropdown>
            </td>

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

                        @include('livewire.admin.prodi-management.modal-form.prodi-menu', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.prodi-management.modal-form.prodi-menu', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString
                    ])

                </flux:dropdown>
            </td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($filter) {
                    'prodi' => 7,
                    'jurusan' => 5,
                    'fakultas' => 4,
                    default => 7,
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
