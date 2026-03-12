<div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-auto">

    <div class="flex flex-col sm:flex-row gap-2 mt-2">

        {{-- @if ($xType === 'file') --}}
        <template x-if="$store.config.typeModal == 'file'" x-cloak>
            <flux:button type="submit" variant="primary"
                class="cursor-pointer w-full sm:w-auto bg-green-600 hover:bg-green-700 border-none">
                <span wire:loading.remove wire:target="saveAllRows">
                    Simpan Semua Data {{ (isset($parsedRows) && count($parsedRows) > 0) ? '(' . count($parsedRows) . ' Baris)' : null }}
                </span>
                <span wire:loading wire:target="saveAllRows">
                    Menyimpan...
                </span>
            </flux:button>
        {{-- @else --}}
        </template>
        <template x-if="$store.config.typeModal !== 'file'" x-cloak>
            <flux:button type="submit" variant="primary"
                class="cursor-pointer w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 border-none">
                <span x-text="$store.config.isEdit ? 'Perbarui Data' : 'Simpan Data'" wire:loading.remove wire:target="{{ $isEditing ? $updateX : $saveX }}">
                </span>
                <span wire:loading wire:target="{{ $isEditing ? $updateX : $saveX }}">
                    Memproses...
                </span>
            </flux:button>
        </template>
        {{-- @endif --}}

        <flux:modal.close>
            <flux:button variant="primary"
                class="cursor-pointer w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 border-none">
                Batal
            </flux:button>
        </flux:modal.close>

    </div>
</div>
