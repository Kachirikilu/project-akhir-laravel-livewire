@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            if ($typeXString == 'ref') {
                $typeUpper = 'Ref';
            } else {
                $typeUpper = strtoupper($typeXString);
            }
            $editCall = "edit{$typeUpper}($x->id)";
            $deleteCall = "delete{$typeUpper}($x->id, " . ($isTrashed ? 'true' : 'false') . ')';
            $restoreCall = "restore{$typeUpper}($x->id)";

            $typeX2String = $typeXString;
            if ($typeX2String == 'scpmk') {
                $typeX2String = 'Sub-CPMK';
            } elseif ($typeX2String == 'ref') {
                $typeX2String = 'Referensi';
            }
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $x->kode,
            'typeXString' => 'Kode ' . $typeX2String,
        ])

        <flux:menu.separator />

        @if ($typeXString == 'rps')
            @include('livewire.staff.rps-management.modal-form.rps-partial.rps-menu')
        @elseif ($typeXString == 'cpmk')
            @include('livewire.staff.rps-management.modal-form.cpmk-partial.cpmk-menu')
        @elseif ($typeXString == 'scpmk')
            @include('livewire.staff.rps-management.modal-form.scpmk-partial.scpmk-menu')
        @elseif ($typeXString == 'cpl')
            @include('livewire.staff.rps-management.modal-form.cpl-partial.cpl-menu')
        @elseif ($typeXString == 'ref')
            @include('livewire.staff.rps-management.modal-form.ref-partial.ref-menu')
        @endif

    </flux:menu>
@endif
