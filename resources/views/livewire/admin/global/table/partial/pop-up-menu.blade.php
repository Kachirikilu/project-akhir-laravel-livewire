<flux:menu>
    @if (Auth::user()?->admin) @php
        $typeParam = isset($typeXString) ? ", '$typeXString'" : '';
        $editCall = "{$editString}({$x->id}{$typeParam})";
        $deleteCall = "{$confirmDeleteString}({$x->id}{$typeParam})";
    @endphp

        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.config?.resetSelect();

                const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}';
                const editMode = '{{ $editString }}';

                $store.config?.setType(type);
                $store.config?.setEdit(1);

                const colors = {
                    admin: 'text-red-700',
                    dosen: 'text-lime-700',
                    mahasiswa: 'text-cyan-700',
                    prodi: 'text-emerald-700',
                    jurusan: 'text-amber-700',
                    fakultas: 'text-indigo-700'
                };
                $store.config?.setColor(colors[type] ?? 'text-gray-700');

                if (editMode == 'editUser') {
                    $store.config?.setValueUser(
                        '{{ $x->email ?? '' }}',
                        '',
                        '{{ $x->name ?? '' }}',
                        '{{ $detail->nip ?? '' }}',
                        '{{ $detail->nitk ?? '' }}',
                        '{{ $detail->nidn ?? '' }}',
                        '{{ $detail->nidk ?? '' }}',
                        '{{ $detail->nim ?? '' }}',
                        '{{ $detail->tahun_angkatan ?? '' }}',
                        '{{ $detail->status ?? '' }}',
                        '{{ $detail->prodi_id ?? '' }}',
                        '{{ $detail->prodi->prodi ?? '' }}',
                        '{{ $detail->prodi->kode ?? '' }}'
                    );
                    $flux.modal('user-modal').show();
                } else if (editMode == 'editProdi') { 
                    $store.config?.setValueProdi(
                        '{{ $x->prodi ?? '' }}',
                        '{{ $x->strata ?? '' }}',
                        '{{ $x->jurusan_id ?? '' }}',
                        '{{ $x->jurusan ?? '' }}',
                        '{{ $x->fakultas_id ?? '' }}',
                        '{{ $x->fakultas ?? '' }}',
                        '{{ $x->kode ?? '' }}',
                        '{{ $x->kode ?? '' }}',
                        '{{ $x->kode ?? '' }}'
                    );
                    $flux.modal('prodi-modal').show();
                }
            "
            wire:click="{{ $editCall }}" class="!text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span class="cursor-pointer">Edit Data</span>
                <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4" />
            </div>
        </flux:menu.item>

        {{-- Logika Tombol Hapus --}}
        @if (Auth::id() != $x->id || ($nameXString ?? '') != 'Pengguna')
            <flux:menu.separator />

            <flux:menu.item
                @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                    const deleteMode = '{{ $confirmDeleteString }}';

                    if (deleteMode == 'deleteUser') {
                        $store.config?.setDeleteUser(
                            '{{ $x->email ?? '' }}'
                        );
                        $flux.modal('user-delete').show();
                    } else if (deleteMode == 'deleteProdi') { 
                        $store.config?.setDeleteProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->jurusan ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $typeXString ?? '' }}'
                        );
                        $flux.modal('prodi-delete').show();
                    }
                "
                wire:click="{{ $deleteCall }}" class="!text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>
        @endif {{-- Ini penutup @if logika hapus --}}

    @endif {{-- Ini penutup @if Auth admin --}}

</flux:menu>
