@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editRPS($x->id, $typeXString)";
            $deleteCall = "deleteRPS($x->id, $isTrashed)";
            $restoreCall = "restoreRPS($x->id)";
        @endphp

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                $store.rps?.resetSelect();

                const type = '{{ $typeXString }}';
                console.log('Nilai type saat ini:', type);

                $store.rps?.setType(type);
                $store.rps?.setEdit(1);

                const colors = {
                    '1': 'text-emerald-700 dark:text-emerald-400',
                    '2': 'text-amber-700 dark:text-amber-400',
                    '3': 'text-indigo-700 dark:text-indigo-400',
                    '4': 'text-red-700 dark:text-red-400'
                };
                $store.rps?.setColor(colors[type] ?? 'text-gray-700');

                    $store.rps?.setValueMK(
                        '{{ $mk->tingkatan_mk ?? '' }}',
                        '{{ $mk->mk ?? '' }}',
                        '{{ $mk->kode_blok ?? '' }}',
                        '{{ $mk->digit_semester ?? '' }}',
                        '{{ $mk->digit_mk ?? '' }}',
                        '{{ $mk->nama_pr ?? '' }}',
                        '{{ $mk->id_prodi ?? '' }}',
                        '{{ $mk->kode_pr ?? '' }}',
                        '{{ $mk->semester ?? '' }}',
                        '{{ $mk->sks ?? '' }}',
                        '{{ $mk->tipe_sks ?? '' }}',
                        '{{ $mk->wajib ?? '' }}'
                    );

                    $flux.modal('mk-modal').show();
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
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="{{ $restoreCall }}"
                class="!text-yellow-700 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Restore {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $restoreCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                            $store.rps?.setDeleteProdi(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('mk-delete').show();
                "
                wire:click="{{ $deleteCall }}"
                class="!text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus Permanen {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>
        @endif

    </flux:menu>
@endif
