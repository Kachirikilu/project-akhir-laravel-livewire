<flux:menu>
    @if (Auth::user()?->admin)
        @php
            $typeParam = isset($typeXString) ? $typeXString : strtolower($x->role);

            if ($editString == 'editProdi') {
                $editCall = "{$editString}({$x->id}, '{$typeParam}')";
                $deleteCall = "{$confirmDeleteString}({$x->id}, '{$typeParam}')";
            } else {
                $editCall = "{$editString}({$x->id})";
                $deleteCall = "{$confirmDeleteString}({$x->id})";
            }
        @endphp

        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                {{-- const type = '{{ $typeParam }}';
                const editMode = '{{ $editString }}'; --}}
                const type = '{{ strtolower($x->role) }}'';
                const editMode = @js($editString);

                $store.config?.setType(type);
                $store.config?.setEdit(1);

                const colors = {
                    admin: 'text-red-700',
                    dosen: 'text-lime-700',
                    mahasiswa: 'text-cyan-700',
                    prodi: 'text-red-700',
                    jurusan: 'text-lime-700',
                    fakultas: 'text-cyan-700'
                }

                $store.config?.setColor(colors[type] ?? 'text-gray-700')

                {{-- if (editMode == 'editUser') { --}}
                
                    $store.config?.setValueUser(
                        '{{ $x->email }}',
                        '',
                        '{{ $x->name }}',
                        '{{ $detail?->nip }}',
                        '{{ $detail?->nitk }}',
                        '{{ $detail?->nidn }}',
                        '{{ $detail?->nidk }}',
                        '{{ $detail?->nim }}',
                        '{{ $detail?->tahun_angkatan }}',
                        '{{ $detail?->status }}',
                        '{{ $detail?->prodi_id }}',
                        '{{ $detail?->prodi->prodi }}'
                    );
                    $flux.modal('user-modal').show(); 
                    
                {{-- } elseif (editMode == 'editProdi') {
                    $store.config?.setValueProdi(
                        '{{ $x->prodi }}',
                        '{{ $x->strata }}',
                        '{{ $x->jurusan_id }}',
                        '{{ $x->jurusan }}',
                        '{{ $x->fakultas_id }}',
                        '{{ $x->fakultas }}'
                    );
                    $flux.modal('prodi-modal').show();
                } --}}
            "
            wire:click="{{ $editCall }}" class="!text-yellow-600 hover:!bg-yellow-100">
            <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span class="cursor-pointer">Edit Data</span>
                <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4" />
            </div>
        </flux:menu.item>

        {{-- Logika Tombol Hapus --}}
        @if (Auth::id() != $x->id || ($nameXString ?? '') != 'Pengguna')
            <flux:menu.separator />

            <flux:menu.item wire:click="{{ $deleteCall }}" class="!text-red-800 hover:!bg-red-50">
                <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus {{ $nameXString ?? 'Data' }}</span>
                    <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                        class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>
        @endif {{-- Ini penutup @if logika hapus --}}

    @endif {{-- Ini penutup @if Auth admin --}}
</flux:menu>
