<flux:menu>
    @if (Auth::user()?->admin) @php
        $editCall = "{$editString}({$x->id})";
        $deleteCall = "{$confirmDeleteString}({$x->id})";
    @endphp

        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.config?.resetSelect();

                const type = '{{ strtolower($x->role) }}';

                $store.config?.setType(type);
                $store.config?.setEdit(1);

                const colors = {
                    admin: 'text-red-700',
                    dosen: 'text-lime-700',
                    mahasiswa: 'text-cyan-700',
                };
                $store.config?.setColor(colors[type] ?? 'text-gray-700');

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

                        $store.config?.setDeleteUser(
                            '{{ $x->email ?? '' }}'
                        );
                        $flux.modal('user-delete').show();
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
