@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editRPS($x->id)";
            $deleteCall = "deleteRPS($x->id, $isTrashed)";
            $restoreCall = "restoreRPS($x->id)";

            $typeX2String = $typeXString;
            if ($typeX2String == 'scpmk') {
                $typeX2String = 'Sub-CPMK';
            } else  if ($typeX2String == 'ref') {
                $typeX2String = 'Referensi';
            }
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $x->kode,
            'typeXString' => 'Kode ' . $typeX2String
        ])

        <flux:menu.separator />


        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                const type = '{{ $typeXString }}';

                $store.rps?.setEdit(1);

                $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.rps?.setValueRPS(
                        '{{ $x->kode ?? '' }}',
                        '{{ $x->kode_blok ?? '' }}',
                        '{{ $x->deskripsi ?? '' }}',
                        '{{ $x->mk_id ?? '' }}',
                        '{{ $x->kode_mk ?? '' }}',
                        '{{ $x->mk ?? '' }}',
                        '{{ $x->akademik ?? '' }}',
                        '{{ $x->draf ?? '' }}',
                        '{{ $x->count_scpmk }}',
                        '{{ $x->total_bobot }}'
                    );

                    $flux.modal('rps-modal').show();
            "
                wire:click="{{ $editCall }}"
                class="!text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Edit Data</span>
                    <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}

                        $store.rps?.setDeleteProdi(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
                "
                wire:click="{{ $deleteCall }}"
                class="!text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>
        @else
        @endif

    </flux:menu>
@endif
