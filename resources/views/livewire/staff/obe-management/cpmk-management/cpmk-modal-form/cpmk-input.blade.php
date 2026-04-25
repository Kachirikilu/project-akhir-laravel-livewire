<div x-data="{ step: 1 }">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'CPMK', 2 => 'Sub-CPMK', 3 => 'Referensi', 4 => 'RPS Terkait'],
        'errorsCount' => $this->getCPMKErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-partial.cpmk-main-input')
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-partial.cpmk-cpl-input')
        </div>

        <div x-show="step === 2">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-partial.cpmk-scpmk-input')
        </div>

        <div x-show="step === 3">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-partial.cpmk-ref-input')
        </div>

        <div x-show="step === 4">
            {{-- <template x-if="$store.cpmk?.isEdit == 1"> --}}
            @include('livewire.staff.obe-management.obe-partial.rps-list', [
                'alpine' => 'cpmk',
                'rps_items_list' => $cpmk_rps_items_list,
                'rps_modal_paginator' => $cpmk_rps_modal_paginator,
                'nameXString' => 'CPMK',
            ])
            {{-- </template> --}}
        </div>

    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 4,
    ])

</div>
