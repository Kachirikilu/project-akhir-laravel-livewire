@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editMK($x->id, $typeXString)";
            $deleteCall = "deleteMK($x->id, $isTrashed)";
            $restoreCall = "restoreMK($x->id)";
        @endphp

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                {{-- $store.mk?.resetSelect(); --}}

                const type = '{{ $typeXString }}';
                console.log('Nilai type saat ini:', type);

                $store.mk?.setType(type);
                $store.mk?.setEdit(1);

                const colors = {
                    '1': 'text-emerald-700 dark:text-emerald-400',
                    '2': 'text-amber-700 dark:text-amber-400',
                    '3': 'text-indigo-700 dark:text-indigo-400',
                    '4': 'text-red-700 dark:text-red-400'
                };
                $store.mk?.setColor(colors[type] ?? 'text-gray-700');

                    $store.mk?.setValueMK(
                        '{{ $matkul->tingkatan_mk ?? '' }}',
                        '{{ $matkul->matkul ?? '' }}',
                        '{{ $matkul->kode_blok ?? '' }}',
                        '{{ $matkul->digit_semester ?? '' }}',
                        '{{ $matkul->digit_mk ?? '' }}',
                        '{{ $matkul->nama_prodi ?? '' }}',
                        '{{ $matkul->id_prodi ?? '' }}',
                        '{{ $matkul->kode_pr ?? '' }}',
                        '{{ $matkul->semester ?? '' }}',
                        '{{ $matkul->sks ?? '' }}',
                        '{{ $matkul->tipe_sks ?? '' }}',
                        '{{ $matkul->wajib ?? '' }}'
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

                        $store.mk?.setDeleteMK(
                            '{{ $x->matkul ?? '' }}',
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
                            $store.mk?.setDeleteMK(
                            '{{ $x->matkul ?? '' }}',
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
