<div x-data="{ kode: @entangle($kodeString ?? null).live }">
    <label class="block text-sm font-medium">Kode RPS</label>
    <div class="relative mt-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="academic-cap" variant="mini" x-bind:class="$store.rps?.colorIcon" />
        </div>

        <input type="text" readonly
            @if($kodeString ?? null)
                x-bind:value="kode || '---------'"
            @else 
                value="UNI-----"
            @endif
            placeholder="---------"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
    </div>
</div>
