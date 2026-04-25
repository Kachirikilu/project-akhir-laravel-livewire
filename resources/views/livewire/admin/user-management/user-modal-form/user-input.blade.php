<div x-data="{ step: 1 }">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Akun', 2 => 'Personal'],
        'errorsCount' => $this->getUserErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-main-input')

        </div>
        <div x-show="step === 2">
            <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
                @include('livewire.admin.user-management.user-modal-form.user-input-partial.admin-input')
            </template>
            <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
                @include('livewire.admin.user-management.user-modal-form.user-input-partial.dosen-input')
            </template>
            <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
                @include('livewire.admin.user-management.user-modal-form.user-input-partial.mahasiswa-input')
            </template>
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 2,
    ])
</div>
