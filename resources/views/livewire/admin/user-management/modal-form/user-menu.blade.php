@if (Auth::user()?->admin)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editUser($x->id)";
            $deleteCall = "deleteUser($x->id, $isTrashed)";
            $restoreCall = "restoreUser($x->id)";
        @endphp

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                $store.user?.resetSelect();

                const type = '{{ strtolower($x->role) }}';

                $store.user?.setType(type);
                $store.user?.setEdit(1);

                const colors = {
                    admin: 'text-red-700 dark:text-red-400',
                    dosen: 'text-lime-700 dark:text-lime-400',
                    mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                };
                $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                    $store.user?.setValueUser(
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
                wire:click="{{ $editCall }}"
                class="!text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
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

                        $store.user?.setDeleteUser(
                            '{{ $x->email ?? '' }}'
                        );
                        $flux.modal('user-delete').show();
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
            @endif {{-- Ini penutup @if logika hapus --}}
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
                        $store.user?.setDeleteUser(
                            '{{ $x->email ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('user-delete').show();
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
