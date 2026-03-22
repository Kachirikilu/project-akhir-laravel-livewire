<x-admin.global.table.main-layout-table>

    <x-slot:header>
        {{-- ID - Sorting Angka --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'id'])
        {{-- Prodi - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'prodi'])
        {{-- Jurusan - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'jurusan'])
        {{-- Fakultas - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'fakultas'])
        {{-- Strata - Sorting A-Z --}}
        @include('livewire.admin.global.table.head-table', ['sortFieldString' => 'strata'])
        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
    </x-slot:header>


    @forelse($prodis as $prodi)
        <tr wire:key="prodi-{{ $prodi->id }}" class="hover:bg-gray-50" data-prodi-id="{{ $prodi->id }}">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $prodi->id }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->prodi ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->jurusan ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ $prodi->fakultas ?? '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">
                <flux:dropdown>
                    <button>
                        @switch($prodi->strata)
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
                                <flux:badge icon="academic-cap" size="sm">{{ $prodi->strata }}</flux:badge>
                        @endswitch
                    </button>

                    <flux:menu>
                        @if (Auth::user()?->admin)
                            <flux:menu.item wire:click="editUser({{ $prodi->id }})"
                                class="!text-yellow-600 hover:!bg-yellow-100">
                                <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

                                <div class="flex justify-between items-center w-full">
                                    <span>Edit Data</span>
                                    <flux:icon wire:loading wire:target="editUser({{ $prodi->id }})"
                                        name="arrow-path" class="animate-spin h-4 w-4" />
                                </div>
                            </flux:menu.item>

                            <flux:menu.separator />
                            <flux:menu.item wire:click="confirmDelete({{ $prodi->id }})"
                                class="!text-red-800 hover:!bg-red-50">
                                <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                                <div class="flex justify-between items-center w-full">
                                    <span>Hapus Program Studi</span>
                                    <flux:icon wire:loading wire:target="confirmDelete({{ $prodi->id }})"
                                        name="arrow-path" class="animate-spin h-4 w-4" />
                                </div>
                            </flux:menu.item>
                        @endif
                    </flux:menu>
                </flux:dropdown>
            </td>

            <td class="px-6 py-4 text-center text-sm space-x-2 gap-2">
                <flux:dropdown>
                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                    </flux:button>

                    <flux:menu>
                        @if (Auth::user()?->admin)
                            <flux:menu.item wire:click="editUser({{ $prodi->id }})"
                                class="!text-yellow-600 hover:!bg-yellow-100">
                                <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

                                <div class="flex justify-between items-center w-full">
                                    <span>Edit Data</span>
                                    <flux:icon wire:loading wire:target="editUser({{ $prodi->id }})"
                                        name="arrow-path" class="animate-spin h-4 w-4" />
                                </div>
                            </flux:menu.item>

                            <flux:menu.separator />
                            <flux:menu.item wire:click="confirmDelete({{ $prodi->id }})"
                                class="!text-red-800 hover:!bg-red-50">
                                <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                                <div class="flex justify-between items-center w-full">
                                    <span>Hapus Program Studi</span>
                                    <flux:icon wire:loading wire:target="confirmDelete({{ $prodi->id }})"
                                        name="arrow-path" class="animate-spin h-4 w-4" />
                                </div>
                            </flux:menu.item>
                        @endif


                    </flux:menu>
                </flux:dropdown>
            </td>
        </tr>

        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada program studi ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.admin.global.table.footer-table', [
                'typeXString' => $prodis,
            ])
        </x-slot:footer>

    </x-admin.global.table.main-layout-table>
