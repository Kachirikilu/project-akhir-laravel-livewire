<div x-data="{}"
    x-effect="
                    if ($store.rps) {
                        let ta1 = parseInt($store.rps.tahun_akademik_1);

                        if (!ta1) {
                            $store.rps.tahun_akademik_2 = '';
                        } else {
                            $store.rps.tahun_akademik_2 = ta1 + 1;
                        }
                    }
                ">
    <div class="relative mt-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="variable" variant="mini" x-bind:class="$store.rps?.colorIcon" />
        </div>

        <input type="text" x-bind:value="$store.rps?.tahun_akademik_2 || '----'" readonly placeholder="----"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
    </div>
</div>
