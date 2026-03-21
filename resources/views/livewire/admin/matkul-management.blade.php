<div class="p-6 bg-gray-50 dark:bg-neutral-900 rounded-xl shadow-sm transition-colors duration-300">
    @include('livewire.admin.matkul-management.matkul-toolbar')
    @include('livewire.admin.matkul-management.matkul-switch-table')

    @include('livewire.admin.matkul-management.matkul-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.matkul-management.matkul-table')
    </div>

    {{-- @include('livewire.admin.user-management.user-modal-form')
    @include('livewire.admin.user-management.user-modal-delete') --}}
</div>
