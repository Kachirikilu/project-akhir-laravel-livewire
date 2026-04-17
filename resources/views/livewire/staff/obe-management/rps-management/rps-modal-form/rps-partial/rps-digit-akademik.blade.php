<div x-data="{}"
    x-effect="
        if ($store.rps) {
            let ta2 = parseInt($store.rps.akademik_2);

            if (!ta2) {
                $store.rps.digit_akademik = '';
            } else {
                if (ta2 >= 3000) {
                    $store.rps.digit_akademik = String(ta2);
                } else if (ta2 >= 2100) {
                    $store.rps.digit_akademik = String(ta2).slice(-3);
                } else {
                    $store.rps.digit_akademik = String(ta2).slice(-2);
                }
            }
        }
    ">
    @include('livewire.global.modal-form.digit-input', [
        'alpine' => 'rps',
        'modelString' => 'digit_akademik',
    ])
</div>
