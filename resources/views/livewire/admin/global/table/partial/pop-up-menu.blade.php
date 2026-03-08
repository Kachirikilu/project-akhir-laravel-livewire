<flux:menu>
    @if (Auth::user()?->admin)
        <flux:menu.item wire:click="{{ $editString }}({{ $x->id }})"
            class="!text-yellow-600 hover:!bg-yellow-100">
            <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span class="cursor-pointer">Edit Data</span>
                <flux:icon wire:loading wire:target="{{ $editString }}({{ $x->id }})" name="arrow-path"
                    class="animate-spin h-4 w-4" />
            </div>
        </flux:menu.item>

        @if (Auth::id() != $x->id || $nameXString != 'Pengguna')
            <flux:menu.separator />
            <flux:menu.item wire:click="{{ $confirmDeleteString }}({{ $x->id }})"
                class="!text-red-800 hover:!bg-red-50">
                <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span class="cursor-pointer">Hapus {{ $nameXString }}</span>
                    <flux:icon wire:loading wire:target="{{ $confirmDeleteString }}({{ $x->id }})"
                        name="arrow-path" class="animate-spin h-4 w-4" />
                </div>
            </flux:menu.item>
        @endif
    @endif
</flux:menu>
