<div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-start gap-2 w-full sm:w-auto mt-auto">

    <div class="flex flex-col sm:flex-row gap-2 mt-2">

        @if ($prodiType === 'file')
            {{-- @if (!empty($parsedRows)) --}}
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    class="cursor-pointer w-full sm:w-auto bg-green-600 hover:bg-green-700 border-none">
                    <span wire:loading.remove wire:target="saveAllRows">
                        Simpan Semua Data {{ count($parsedRows) > 0 ? '(' . count($parsedRows) . ' Baris)' : null }}
                    </span>
                    <span wire:loading wire:target="saveAllRows">
                        Menyimpan...
                    </span>
                </flux:button>
            {{-- @endif --}}
        @else
            <flux:button type="submit" variant="primary"
                wire:loading.attr="disabled" class="cursor-pointer w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 border-none">
                <span wire:loading.remove wire:target="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}">
                    {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                </span>
                <span wire:loading wire:target="{{ $isEditing ? 'updateProdi' : 'saveProdi' }}">
                    Memproses...
                </span>
            </flux:button>
        @endif

        <flux:modal.close>
            <flux:button variant="primary"
                class="cursor-pointer w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 border-none">
                Batal
            </flux:button>
        </flux:modal.close>

    </div>
</div>
