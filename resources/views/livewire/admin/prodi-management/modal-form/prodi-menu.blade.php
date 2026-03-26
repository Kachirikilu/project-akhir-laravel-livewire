@if (Auth::user()?->admin)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editProdi($x->id, '$typeXString')";
            $deleteCall = "deleteProdi($x->id, '$typeXString', $isTrashed)";
            $restoreCall = "restoreProdi($x->id, '$typeXString')";
        @endphp

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.config?.resetSelect();

                    const type = '{{ $typeXString }}';

                    $store.config?.setType(type);
                    $store.config?.setEdit(1);

                    const colors = {
                        prodi: 'text-emerald-700 dark:text-emerald-400',
                        jurusan: 'text-amber-700 dark:text-amber-400',
                        fakultas: 'text-indigo-700 dark:text-indigo-400'
                    };
                    $store.config?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                        $store.config?.setValueProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->strata ?? '' }}',
                            '{{ $x->jurusan_id ?? '' }}',
                            '{{ $x->jurusan ?? '' }}',
                            '{{ $x->fakultas_id ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('prodi-modal').show();
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

            {{-- Logika Tombol Hapus --}}
            <flux:menu.item
                @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                        $store.config?.setDeleteProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->jurusan ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $typeXString ?? '' }}'
                        );
                        $flux.modal('prodi-delete').show();
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
            <flux:menu.item
                wire:click="{{ $restoreCall }}"
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
                        $store.config?.setDeleteProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->jurusan ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $typeXString ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('prodi-delete').show();
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
