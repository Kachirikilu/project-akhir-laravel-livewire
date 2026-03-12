<div class="p-6 bg-gray-50">
    @include('livewire.admin.user-management.user-toolbar')
    @include('livewire.admin.user-management.user-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="filterBy">
        @include('livewire.admin.user-management.user-table')
    </div>

    @include('livewire.admin.user-management.user-modal-form')
    @include('livewire.admin.user-management.user-modal-delete')
</div>