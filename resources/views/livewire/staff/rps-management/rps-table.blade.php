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

            @if ($switchTable === 'rps')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'matkul',
                    'headString' => 'Mata Kuliah'
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'akademik',
                    'headString' => 'Tahun Akademik',
                    'isCenter' => 1
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'is_draf',
                    'headString' => 'Status',
                    'isMain' => 1,
                    'isCenter' => 1
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'revisi',
                    'headString' => 'Tanggal Revisi'
                ])
            @endif

            @if ($switchTable === 'cpmk' || $switchTable === 'scpmk' || $switchTable === 'cpl')
                @include('livewire.global.table.head-table', ['sortFieldString' => 'deskripsi'])
            @endif
            @if ($switchTable === 'scpmk')
                @include('livewire.global.table.head-table', ['sortFieldString' => 'materi'])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'bobot','isMain' => 1, 'isCenter' => 1])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'indikator'])
            @endif
            @if ($switchTable === 'ref')
                @include('livewire.global.table.head-table', ['sortFieldString' => 'judul'])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'penulis'])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'penerbit'])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'tahun','isMain' => 1, 'isCenter' => 1])
                @include('livewire.global.table.head-table', ['sortFieldString' => 'link'])
            @endif

            <th class="{{ $headKolom }} . ' border-x uppercase'">Aksi</th>

            @include('livewire.global.table.head-table', ['sortFieldString' => 'created_at', 'headString' => 'Created At'])
            @include('livewire.global.table.head-table', ['sortFieldString' => 'updated_at', 'headString' => 'Updated At'])

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $x->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($x->tingkatan_mk)
                             @case(1)
                                <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $x->kode ?? '-' }}</flux:badge>
                            @break
                            @case(2)
                                <flux:badge icon="book-open" color="amber" size="sm">{{ $x->kode ?? '-' }}</flux:badge>
                            @break
                            @case(3)
                                <flux:badge icon="building-library" color="indigo" size="sm">{{ $x->kode ?? '-' }}</flux:badge>
                            @break
                            @default
                                <flux:badge icon="globe-alt" color="red" size="sm">{{ $x->kode ?? '-' }}</flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.staff.rps-management.modal-form.rps-menu', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])
                </flux:dropdown>
            </td>

            @if ($switchTable === 'rps')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->matkul ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48 text-center">{{ $x->akademik ?? '-' }}</td>

                <td class="{{ $mainKolom }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if($x->is_draf == 0)
                                <flux:badge color="green" size="sm">
                                    Aktif
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    Draf
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.rps-management.modal-form.rps-menu', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>
                <td class="{{ $secondKolom }}">{{ $x->revisi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'cpmk' || $switchTable === 'scpmk' || $switchTable === 'cpl')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->deskripsi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'scpmk')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->materi ?? '-' }}</td>
                <td class="{{ $mainKolom }} text-center">{{ $x->bobot ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->indikator ?? '-' }}</td>
            @endif

            @if ($switchTable === 'ref')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->judul ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->penulis ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->penerbit ?? '-' }}</td>
                <td class="{{ $mainKolom }} text-center">{{ $x->tahun ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->link ?? '-' }}</td>
            @endif

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.rps-management.modal-form.rps-menu', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])

                </flux:dropdown>
            </td>


            <td class="{{ $secondKolom }} min-w-48">{{ $x->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} min-w-48">{{ $x->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($switchTable) {
                    'rps' => 9,
                    'cpmk' => 6,
                    'scpmk' => 9,
                    'cpl' => 6,
                    'ref' => 10,
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
