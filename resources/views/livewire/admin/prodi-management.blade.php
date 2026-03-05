<div class="p-6 mb-6 bg-gray-50" x-data="{ show: @entangle('showModal') }">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2>
    {{-- @include('livewire.admin.prodi-management.toolbar') --}}
    @include('livewire.admin.prodi-management.switch-table')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.prodi-management.search-and-filters')
        @if ($this->switchTable === 'prodi')
            @include('livewire.admin.prodi-management.prodi-table')
        @elseif ($this->switchTable === 'jurusan')
            @include('livewire.admin.prodi-management.jurusan-table')
        @elseif ($this->switchTable === 'fakultas')
            @include('livewire.admin.prodi-management.fakultas-table')
        @endif
    </div>

    {{-- @include('livewire.admin.prodi-management.modal-form') --}}
    {{-- @include('livewire.admin.prodi-management.modal-delete') --}}
</div>