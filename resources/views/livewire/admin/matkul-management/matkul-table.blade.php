<x-admin.global.table.main-layout-table>

    @php
        if ($switchTable !== '') {
            $borderLeft = 'border-r border-gray-300 dark:border-neutral-700';
            $isBorderLeft = 1;
        } else {
            $borderLeft = '';
            $isBorderLeft = 0;
        }
    @endphp

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr class="bg-gray-50 dark:bg-neutral-800/50">

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
                class="border-x border-b border-gray-300 dark:border-neutral-700 dark:border-neutral-600 bg-gray-50/50 dark:bg-neutral-700/50 px-6 py-2 text-center text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider transition-colors">
                Bobot Mata Kuliah (SKS)
            </th>

            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            <th rowspan="2" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>

        {{-- BARIS KEDUA (Hanya untuk detail SKS) --}}
        <tr class="bg-gray-50">
            @include('livewire.admin.global.table.head-table', [
                'sortFieldString' => 'sks',
                'headString' => 'Total',
                'isSubHeader' => 1,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($switchTable == 'tatap_muka' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_tm',
                    'headString' => 'Tatap Muka',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderLeft,
                ])
            @endif
            @if ($switchTable == 'praktikum' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_pr',
                    'headString' => 'Praktikum',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderLeft,
                ])
            @endif
            @if ($switchTable == 'praktek_lapangan' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_pl',
                    'headString' => 'Praktek Lapangan',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderLeft,
                ])
            @endif
            @if ($switchTable == 'simulasi' || $switchTable == '')
                @include('livewire.admin.global.table.head-table', [
                    'sortFieldString' => 'sks_sm',
                    'headString' => 'Simulasi',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($matkuls as $matkul)
        <tr wire:key="matkul-{{ $matkul->id }}"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors duration-200"
            data-matkul-id="{{ $matkul->id }}">
            <td class="px-6 py-4 text-center text-sm font-medium">{{ $matkul->id }}</td>
            <td
                class="px-6 py-4 border-x border-gray-300 dark:border-neutral-700 bg-gray-100/30 dark:bg-neutral-700/30 text-center text-sm text-gray-700 dark:text-gray-200">
                {{ $matkul->kode ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $matkul->nama_matkul ?? '-' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-200">{{ $matkul->semester ?? '-' }}
            </td>

            {{-- <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $matkul->sks ?? '-' }}</td> --}}
            <td
                class="px-6 py-4 border-x border-gray-300 dark:border-neutral-700 bg-gray-100/30 dark:bg-neutral-700/30 text-center font-bold text-sm text-gray-700 dark:text-gray-200">
                {{ $matkul->sks ?? '-' }}</td>

            @if ($switchTable == 'tatap_muka' || $switchTable == '')
                <td
                    class="px-6 py-4 {{ $borderLeft }} text-center text-sm bg-gray-50/30 dark:bg-neutral-600/30 text-gray-700 dark:text-gray-200">
                    {{ $matkul->sks_tm ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktikum' || $switchTable == '')
                <td
                    class="px-6 py-4 {{ $borderLeft }} text-center text-sm bg-gray-50/30 dark:bg-neutral-600/30 text-gray-700 dark:text-gray-200">
                    {{ $matkul->sks_pr ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktek_lapangan' || $switchTable == '')
                <td
                    class="px-6 py-4 {{ $borderLeft }} text-center text-sm bg-gray-50/30 dark:bg-neutral-600/30 text-gray-700 dark:text-gray-200">
                    {{ $matkul->sks_pl ?? '-' }}</td>
            @endif

            @if ($switchTable == 'simulasi' || $switchTable == '')
                <td
                    class="px-6 py-4 border-r border-gray-300 dark:border-neutral-700 text-center text-sm bg-gray-50/30 dark:bg-neutral-600/30 text-gray-700 dark:text-gray-200">
                    {{ $matkul->sks_sm ?? '-' }}</td>
            @endif

            <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                @if ($matkul->is_wajib)
                    <flux:badge icon="check" color="green" size="sm" inset="top bottom">Wajib</flux:badge>
                @else
                    <flux:badge icon="x-mark" color="zinc" size="sm" inset="top bottom">Pilihan</flux:badge>
                @endif
            </td>


            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $matkul,
                'typeXString' => $switchTable,
                'editString' => 'editMK',
                'nameXString' => 'Mata Kuliah',
                'confirmDeleteString' => 'deleteMK',
            ])
        </tr>
    @empty
        <tr>
            <td colspan="{{ $switchTable == '' ? 11 : 8 }}" class="px-6 py-4 text-center text-gray-500">
                Tidak ada Mata Kuliah ditemukan!
            </td>
        </tr>
    @endforelse


    <x-slot:footer>
        @include('livewire.admin.global.table.footer-table', [
            'typeOfXString' => $matkuls,
        ])
    </x-slot:footer>

</x-admin.global.table.main-layout-table>
