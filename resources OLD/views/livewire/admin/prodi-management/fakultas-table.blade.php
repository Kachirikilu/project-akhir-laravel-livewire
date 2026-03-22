<x-admin.global.table.main-layout-table>

    <x-slot:header>
            {{-- ID - Sorting Angka --}}
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])
            {{-- Fakultas - Sorting A-Z --}}
            @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($fakultass as $fakultas)
        <tr wire:key="fakultas-{{ $fakultas->id }}" class="hover:bg-gray-50" data-fakultas-id="{{ $fakultas->id }}">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $fakultas->id }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">Fakultas {{ $fakultas->fakultas ?? '-' }}</td>

            @include('livewire.admin.global.table.menu-aksi', [
                'x' => $fakultas,
                'nameXString' => 'Fakultas',
                'editString' => 'editFakultas',
                'confirmDeleteString' => 'confirmDelete',
            ])
        </tr>

    @empty
        <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                Tidak ada fakultas ditemukan!
            </td>
        </tr>
    @endforelse


    <x-slot:footer>
        @include('livewire.admin.global.table.footer-table', [
            'typeXString' => $fakultass
        ])
    </x-slot:footer>

</x-admin.global.table.main-layout-table>
