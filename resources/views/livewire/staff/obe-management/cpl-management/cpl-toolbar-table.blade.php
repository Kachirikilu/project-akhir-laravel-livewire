<div>
    @if (!$isTrashed)
        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.cpl?.reset();
                $store.cpl?.setFlyout(false);

                $store.cpl?.setEdit(1);

                $store.cpl?.setColor('text-red-700 dark:text-red-400');

                $store.cpl?.setValueCPL(
                    '{{ $x->kode_cpl ?? '' }}',
                    '{{ $x->deskripsi ?? '' }}',
                );

                $flux.modal('cpl-modal').show();
            "
            wire:click="{{ $editCall }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit Data</span>
                <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        <flux:menu.item
            @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}

                        $store.cpl?.setDeleteProdi(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
                "
            wire:click="{{ $deleteCall }}"
            class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
            <flux:icon name="trash" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Hapus {{ $nameXString ?? 'Data' }}</span>
                <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>
    @else
    @endif

</div>
