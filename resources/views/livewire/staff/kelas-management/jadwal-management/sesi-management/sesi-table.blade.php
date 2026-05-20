{{-- Sesi Section --}}

<div class="flex flex-wrap items-center gap-2 mb-4">
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" size="sm"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] transition-all duration-200 ease-in-out"
                wire:target="addSesi">
                Tambah Sesi
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
                <flux:menu.heading>Tambah Sesi</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.sesi?.setEdit(0);
                        $store.sesi?.setColor('text-amber-700 dark:text-amber-400');
                        $flux.modal('sesi-modal').show();
                        $wire.addSesi();
                    "
                    class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                    <flux:icon name="calendar-days" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Sesi Perkuliahan</span>
                        <flux:icon wire:loading wire:target="addSesi()" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

            </flux:menu>
        </flux:dropdown>
    </div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2 mb-5">
    <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2">
        <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
        Sesi Kelas
    </h3>

    <div class="w-full sm:w-auto sm:max-w-md">
        @include('livewire.global.search-and-filters.main-search', [
            'placeholder' => 'Cari Sesi Kelas...',
            'isLive' => 1,
            'isBorder' => 2
        ])
    </div>
</div>

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

    @php
        $borderR = 'border-[var(--border-table-color)] border-r';
        $borderL = 'border-[var(--border-table-color)] border-l';
    @endphp

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            {{-- Kolom yang ditarik ke bawah (Tinggi 2 baris) --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            {{-- @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metode',
                'isCenter' => 1,
                'isMain' => 1,
                'rowSpan' => 2,
            ]) --}}
            <th rowspan="2" class="{{ $headKolom }} border-x">Metode</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'Pertemuan',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            <th colspan="4" class="{{ $headSubKolom }}">
                Informasi Sesi Kelas
            </th>

            <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'created_at',
                'headString' => 'Created At',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'updated_at',
                'headString' => 'Updated At',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
        </tr>

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'hari_pelaksanaan',
                'headString' => 'Hari',
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'jam_pelaksanaan',
                'headString' => 'Jam',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'jumlah_kehadiran',
                'headString' => 'Kehadiran',
                'isCenter' => 1,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'headString' => 'Tanggal',
                'isCenter' => 1,
            ])


        </tr>
    </x-slot:header>


    @forelse($sesis as $s)
        <tr wire:key="kelas-{{ $s->id }}" data-kelas-id="{{ $s->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $s->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($s->metode)
                                @case('Teori')
                                    <flux:badge icon="book-open" color="emerald" size="sm" variant="pill">Teori
                                    </flux:badge>
                                @break

                                @case('Praktik')
                                    <flux:badge icon="beaker" color="cyan" size="sm" variant="pill">Praktik
                                    </flux:badge>
                                @break

                                @case('Tugas')
                                    <flux:badge icon="pencil-square" color="blue" size="sm" variant="pill">Tugas
                                    </flux:badge>
                                @break

                                @case('UTS')
                                @case('UAS')
                                    <flux:badge icon="clipboard-document-check" color="amber" size="sm"
                                        variant="pill">
                                        {{ $s->metode }}</flux:badge>
                                @break

                                @case('Hasil Proyek')
                                    <flux:badge icon="light-bulb" color="indigo" size="sm" variant="pill">Hasil Proyek
                                    </flux:badge>
                                @break

                                @case('Kerja Praktek')
                                    <flux:badge icon="briefcase" color="violet" size="sm" variant="pill">Kerja Praktek
                                    </flux:badge>
                                @break

                                @case('Skripsi')
                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="pill">Skripsi
                                    </flux:badge>
                                @break

                                @case('Aktivitas Partisipasif')
                                    <flux:badge icon="user-group" color="rose" size="sm" variant="pill">Partisipasif
                                    </flux:badge>
                                @break

                                @case('Mandiri')
                                    <flux:badge icon="user" color="slate" size="sm" variant="pill">Mandiri
                                    </flux:badge>
                                @break

                                @default
                                    <flux:badge icon="information-circle" color="zinc" size="sm" variant="pill">
                                        {{ $s->metode ?? '-' }}</flux:badge>
                            @endswitch
                        </button>

                    @include(
                        'livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        [
                            'x' => $s,
                            'editString' => 'editSesi',
                            'nameXString' => 'Sesi',
                            'confirmDeleteString' => 'deleteSesi',
                        ]
                    )

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} text-center whitespace-nowrap">{{ $s->pertemuan_ke }}</td>

            <td class="{{ $mainKolom }} text-center whitespace-nowrap">{{ $s->hari }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $s->jam_pelaksanaan }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $s->kehadirans_count . ' / ' . $jadwal->kapasitas }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $s->tanggal_pelaksanaan }}</td>


            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include(
                        'livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        [
                            'x' => $s,
                            'editString' => 'editSesi',
                            'nameXString' => 'Sesi',
                            'confirmDeleteString' => 'deleteSesi',
                        ]
                    )

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $s->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $s->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="14" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada Sesi Kelas ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $sesis,
            ])
        </x-slot:footer>

        </x-admin.global.table.main-layout-table>
