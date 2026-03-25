<div x-data="{}"
    x-effect="
                    if ($store.config) {
                        let sem = parseInt($store.config.semester);
                        let blok = parseInt($store.config.kode_blok);
                        
                        if (!sem) {
                            $store.config.digit_semester = '';
                        } else if (blok === 0) {
                            $store.config.digit_semester = Math.ceil(sem / 2).toString() + '0';
                        } else {
                            let tahun = Math.ceil(sem / 2);
                            let tipe = (sem % 2 !== 0) ? '1' : '2';
                            $store.config.digit_semester = tahun.toString() + tipe;
                        }
                    }
                ">
    <div class="relative mt-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="variable" variant="mini" x-bind:class="$store.config?.colorIcon" />
        </div>

        <input type="text" x-bind:value="$store.config?.digit_semester || '--'" readonly placeholder="--"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
    </div>
</div>
