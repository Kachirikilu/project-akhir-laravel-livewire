<div class="p-6 bg-gray-50" x-data="{ show: @entangle('showModal') }">
    @include('livewire.admin.user-management.toolbar')
    @include('livewire.admin.user-management.search-and-filters')
    @include('livewire.admin.user-management.user-table')
    @include('livewire.admin.user-management.modal-form')
    @include('livewire.admin.user-management.modal-delete')
</div>