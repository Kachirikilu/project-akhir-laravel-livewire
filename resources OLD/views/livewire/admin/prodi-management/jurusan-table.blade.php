<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])
        {{-- Jurusan - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'jurusan'])
        {{-- Fakultas - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($jurusans as $jurusan)
        <tr wire:key="jurusan-{{ $jurusan->id }}" class="hover:bg-gray-50" data-jurusan-id="{{ $jurusan->id }}">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $jurusan->id }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $jurusan->nama_jurusan ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $jurusan->fakultas ?? '-' }}</td>

            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $jurusan,
                'nameXString' => 'Jurusan',
                'editString' => 'editJurusan',
                'confirmDeleteString' => 'confirmDelete',
            ])
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                Tidak ada jurusan ditemukan!
            </td>
        </tr>
    @endforelse


    <x-slot:footer>
        @include('livewire.admin.global.table.footer-table', [
            'typeXString' => $jurusans
        ])
    </x-slot:footer>

</x-admin.global.table.main-layout-table>
