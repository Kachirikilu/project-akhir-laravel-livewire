    @if (!$isTrashed)
        {{-- Tombol Detail --}}
        <flux:menu.item
            @click="
                $store.rps?.setEdit(1);

                $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.rps?.setValueRPS(
                        '{{ $x->kode_blok ?? '' }}',
                        '{{ $x->deskripsi ?? '' }}',
                        '{{ $x->mk_id ?? '' }}',
                        '{{ $x->kode_mk ?? '' }}',
                        '{{ $x->mk ?? '' }}',
                        '{{ $x->akademik ?? '' }}',
                        '{{ $x->draf ?? '' }}',
                        '{{ $x->count_scpmk }}',
                        '{{ $x->bobot_uts }}',
                        '{{ $x->bobot_uas }}',
                        '{{ $x->total_bobot }}'
                    );

                    $flux.modal('rps-detail-modal').show();
            "
            wire:click="{{ $showCall }}"
            class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-50 dark:hover:!bg-cyan-900/30 transition-colors">
            <flux:icon name="eye" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show Data</span>
                <flux:icon wire:loading wire:target="{{ $showCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.rps?.reset();
                $store.rps?.setFlyout(false);

                $store.rps?.setEdit(1);

                $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.rps?.setValueRPS(
                        '{{ $x->kode_blok ?? '' }}',
                        '{{ $x->deskripsi ?? '' }}',
                        '{{ $x->mk_id ?? '' }}',
                        '{{ $x->kode_mk ?? '' }}',
                        '{{ $x->mk ?? '' }}',
                        '{{ $x->akademik ?? '' }}',
                        '{{ $x->draf ?? '' }}',
                        '{{ $x->count_scpmk }}',
                        '{{ $x->bobot_uts }}',
                        '{{ $x->bobot_uas }}',
                        '{{ $x->total_bobot }}'
                    );

                    $flux.modal('rps-modal').show();
            "
            wire:click="{{ $editCall }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit Data</span>
                <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
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
