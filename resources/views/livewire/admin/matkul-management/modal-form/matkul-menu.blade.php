<flux:menu class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    @if (Auth::user()?->admin) @php
        $editCall = "{$editString}({$x->id}, '{$typeXString}')";
        $deleteCall = "{$confirmDeleteString}({$x->id})";
    @endphp

        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.config?.resetSelect();

                const type = '{{ $typeXString }}';

                $store.config?.setType(type);
                $store.config?.setEdit(1);

                const colors = {
                    prodi: 'text-emerald-700',
                    jurusan: 'text-amber-700',
                    fakultas: 'text-indigo-700'
                };
                $store.config?.setColor(colors[type] ?? 'text-gray-700');

                    $store.config?.setValueMK(
                        '{{ $matkul->tingkatan_mode ?? '' }}',
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
            wire:click="{{ $editCall }}" class="!text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
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

                        $store.config?.setDeleteProdi(
                            '{{ $x->matkul ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
                "
                wire:click="{{ $deleteCall }}" class="!text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>

    @endif {{-- Ini penutup @if Auth admin --}}

</flux:menu>
