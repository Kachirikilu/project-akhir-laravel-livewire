<div class="p-6 mb-6 bg-gray-50" x-data="{ show: @entangle('showModal') }">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Pengguna</h2>
    @include('livewire.admin.user-management.toolbar')
    @include('livewire.admin.user-management.search-and-filters')
    @include('livewire.admin.user-management.user-table')
    @include('livewire.admin.user-management.modal-form')
    @include('livewire.admin.user-management.modal-delete')
</div>