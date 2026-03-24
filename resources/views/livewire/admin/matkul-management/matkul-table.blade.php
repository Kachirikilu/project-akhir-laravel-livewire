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

    @php
        if ($switchTable !== '') {
            $borderRight = 'border-[var(--border-table-color)] border-r';
            $isBorderRight = 1;
        } else {
            $borderRight = '';
            $isBorderRight = 0;
        }
    @endphp

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            {{-- Kolom yang ditarik ke bawah (Tinggi 2 baris) --}}
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'id',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'kode',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'matkul',
                'rowSpan' => 2,
                'headString' => 'Mata Kuliah',
            ])
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'semester',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])

            {{-- Group SKS (Lebar 5 kolom: Total SKS + 4 Tipe SKS) --}}
            <th colspan="{{ $switchTable == '' ? 5 : 2 }}"
                class="{{ $headSubKolom }}">
                Bobot Mata Kuliah (SKS)
            </th>

            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            <th rowspan="2"
                class="{{ $headKolom }}">Aksi</th>
        </tr>

        {{-- BARIS KEDUA (Hanya untuk detail SKS) --}}
        <tr class="bg-gray-50">
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'sks',
                'headString' => 'Total',
                // 'isSubHeader' => 1,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($switchTable == 'tatap_muka' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_tm',
                    'headString' => 'Tatap Muka',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'praktikum' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_pr',
                    'headString' => 'Praktikum',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'praktek_lapangan' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_pl',
                    'headString' => 'Praktek Lapangan',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'simulasi' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_sm',
                    'headString' => 'Simulasi',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($matkuls as $matkul)
        <tr wire:key="matkul-{{ $matkul->id }}" data-matkul-id="{{ $matkul->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">
            
            <td class="{{ $secondKolom }} text-center">{{ $matkul->id }}</td>
            {{-- <td class="{{ $mainKolom }} text-center">{{ $matkul->kode ?? '-' }}</td> --}}

            <td class="{{ $mainKolom  }}">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($matkul->semester)
                            {{-- Tahun 1: Biru/Cyan --}}
                            @case(1)
                                <flux:badge color="blue" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break
                            @case(2)
                                <flux:badge color="cyan" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break

                            {{-- Tahun 2: Hijau/Emerald --}}
                            @case(3)
                                <flux:badge color="green" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break
                            @case(4)
                                <flux:badge color="emerald" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break

                            {{-- Tahun 3: Kuning/Oranye --}}
                            @case(5)
                                <flux:badge color="yellow" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break
                            @case(6)
                                <flux:badge color="orange" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break

                            {{-- Tahun 4: Merah/Ungu (Fase Tugas Akhir) --}}
                            @case(7)
                                <flux:badge color="red" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break
                            @case(8)
                                <flux:badge color="purple" size="sm">{{ $matkul->kode }}</flux:badge>
                                @break
                        @endswitch
                    </button>
                   
                    @include('livewire.admin.matkul-management.modal-form.matkul-menu', [
                        'x' => $matkul,
                        'typeXString' => $matkul->tingkatan_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }}">{{ $matkul->matkul ?? '-' }}</td>
            <td class="{{ $secondKolom }} text-center">{{ $matkul->semester ?? '-' }}</td>

            {{-- <td class="px-6 py-4 text-sm text-[var(--contrast-second-text)]">{{ $matkul->sks ?? '-' }}</td> --}}
            <td class="{{ $mainKolom }} text-center">{{ $matkul->sks ?? '-' }}</td>

            @if ($switchTable == 'tatap_muka' || $switchTable == '')
                <td class="{{ $subKolom }} {{ $borderRight }} text-center">{{ $matkul->sks_tm ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktikum' || $switchTable == '')
                <td
                    class="{{ $subKolom }} {{ $borderRight }} text-center">
                    {{ $matkul->sks_pr ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktek_lapangan' || $switchTable == '')
                <td
                    class="{{ $subKolom }} {{ $borderRight }} text-center">
                    {{ $matkul->sks_pl ?? '-' }}</td>
            @endif

            @if ($switchTable == 'simulasi' || $switchTable == '')
                <td
                    class="{{ $subKolom }} border-r text-center">
                    {{ $matkul->sks_sm ?? '-' }}</td>
            @endif

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @if ($matkul->wajib)
                            <flux:badge icon="check" color="green" size="sm" inset="top bottom">Wajib
                            </flux:badge>
                        @else
                            <flux:badge icon="x-mark" color="zinc" size="sm" inset="top bottom">Pilihan
                            </flux:badge>
                        @endif
                    </button>

                    @include('livewire.admin.matkul-management.modal-form.matkul-menu', [
                        'x' => $matkul,
                        'typeXString' => $matkul->tingkatan_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>


             <td class="{{ $secondKolom }}">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                    </flux:button>
                   
                     @include('livewire.admin.matkul-management.modal-form.matkul-menu', [
                        'x' => $matkul,
                        'typeXString' => $matkul->tingkatan_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ $switchTable == '' ? 11 : 8 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada Mata Kuliah ditemukan!
            </td>
        </tr>
    @endforelse


    <x-slot:footer>
        @include('livewire.admin.global.table.footer-table', [
            'typeXString' => $matkuls,
        ])
    </x-slot:footer>

</x-admin.global.table.main-layout-table>
