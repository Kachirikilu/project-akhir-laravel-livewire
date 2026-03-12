<div class="p-6 bg-gray-50">
    {{-- <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2> --}}
    @include('livewire.admin.prodi-management.prodi-toolbar')
    @include('livewire.admin.prodi-management.prodi-switch-table')

    @include('livewire.admin.prodi-management.prodi-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.prodi-management.prodi-table', [
            'xResults' => match ($this->switchTable) {
                'prodi' => $prodis,
                'jurusan' => $jurusans,
                'fakultas' => $fakultass,
                default => collect([]),
            },
            'xNameString' => match ($this->switchTable) {
                'prodi' => 'Program Studi',
                'jurusan' => 'Jurusan',
                'fakultas' => 'Fakultas',
                default => 'Data',
            },
        ])
    </div>

    @include('livewire.admin.prodi-management.prodi-modal-form')
    {{-- @include('livewire.admin.prodi-management.prodi-modal-delete') --}}
</div>
