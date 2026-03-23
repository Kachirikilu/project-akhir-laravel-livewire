<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.admin.matkul-management.matkul-toolbar')
    @include('livewire.admin.matkul-management.matkul-switch-table')

    @include('livewire.admin.matkul-management.matkul-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.matkul-management.matkul-table')
    </div>

    @include('livewire.admin.matkul-management.matkul-modal-form')
    {{-- @include('livewire.admin.matkul-management.matkul-modal-delete') --}}
</div>
