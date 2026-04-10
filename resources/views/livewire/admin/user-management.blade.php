<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.admin.user-management.user-toolbar')
    @include('livewire.admin.user-management.user-search-and-filters')

    @include('livewire.admin.user-management.user-table')

    @include('livewire.admin.user-management.user-modal-form')
    @include('livewire.admin.user-management.user-modal-delete')
</div>