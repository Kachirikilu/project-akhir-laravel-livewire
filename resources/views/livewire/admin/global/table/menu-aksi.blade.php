<td class="px-6 py-4 text-center text-sm space-x-2 gap-2">
    <flux:dropdown>
        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
        </flux:button>

        <flux:menu>
            @if (Auth::user()?->admin)
                <flux:menu.item wire:click="editUser({{ $typeOfXString->id }})" class="!text-yellow-600 hover:!bg-yellow-100">
                    <flux:icon name="pencil-square" class="!text-yellow-600 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit Data</span>
                        <flux:icon wire:loading wire:target="editUser({{ $typeOfXString->id }})" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />
                <flux:menu.item wire:click="confirmDelete({{ $typeOfXString->id }})" class="!text-red-800 hover:!bg-red-50">
                    <flux:icon name="trash" class="!text-red-800 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus {{ $nameXString }}</span>
                        <flux:icon wire:loading wire:target="confirmDelete({{ $typeOfXString->id }})" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            @endif


        </flux:menu>
    </flux:dropdown>
</td>
