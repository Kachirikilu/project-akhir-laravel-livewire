<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    {{-- @include('livewire.staff.kelas-management.kelas-toolbar') --}}
    @include('livewire.staff.kelas-management.kelas-switch-table')

    @include('livewire.staff.kelas-management.kelas-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.staff.kelas-management.kelas-table')
    </div>

    {{-- @include('livewire.staff.kelas-management.kelas-modal-form')
    @include('livewire.staff.kelas-management.kelas-modal-delete') --}}
</div>
