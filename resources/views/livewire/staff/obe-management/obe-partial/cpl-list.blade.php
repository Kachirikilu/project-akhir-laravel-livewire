<div class="space-y-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="p-2 rounded-lg shadow-sm">
            <flux:icon.academic-cap x-bind:class="$store.rps.colorIcon" variant="solid" class="size-4" />
        </div>
        <div>
            <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">Capaian Pembelajaran
                Lulusan dari CPMK</h3>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Mapping CPL</p>
        </div>
    </div>
    {{-- AREA MAPPING CPL (DI DALAM KOTAK) --}}
    <div
        class="mt-4 p-4 border-2 border-dashed border-[var(--border-table-color)] rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">

        {{-- HEADER: JUDUL & COUNTER --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold uppercase tracking-widest text-gray-400">Mapping CPL:</span>
            <div class="flex items-center gap-2">
                <template x-if="$store.rps.cpl_cpmk.length > 0">
                    <span
                        class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full font-bold uppercase tracking-tighter"
                        x-text="$store.rps.cpl_cpmk.length + ' CPL Terhubung'"></span>
                </template>
            </div>
        </div>

        {{-- LIST CONTAINER --}}
        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1 scrollbar-medium">

            {{-- EMPTY STATE --}}
            <template x-if="$store.rps.cpl_cpmk.length === 0">
                <div class="flex flex-col items-center justify-center py-10 opacity-40">
                    <flux:icon icon="clipboard" variant="outline" class="size-8 mb-2" />
                    <p class="text-xs italic font-medium">Belum ada mapping CPL untuk CPMK ini</p>
                </div>
            </template>

            {{-- ITERASI ITEM --}}
            <template x-for="(cpl, index) in $store.rps.cpl_cpmk" :key="cpl.id">
                <div
                    class="group relative flex items-start gap-3 p-3 bg-[var(--second-table-color)] border border-[var(--border-table-color)] rounded-lg shadow-sm transition-all hover:border-[var(--focus-color)]">

                    {{-- NOMOR URUT --}}
                    <span class="flex-none text-xs font-black text-[var(--hover-focus-color)] w-4 mt-0.5"
                        x-text="index + 1"></span>

                    <div class="flex flex-col gap-1 flex-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-bold px-1.5 py-0.5 mb-0.5 rounded bg-[var(--focus-color)] text-white"
                                x-text="cpl.kode"></span>
                                <span
                                class="text-xs font-bold px-1.5 py-0.5 mb-0.5 rounded bg-[var(--focus-color)] text-white"
                                x-text="'ID'+cpl.id"></span>
                            <div class="h-px flex-1 bg-gray-200 dark:bg-neutral-800 opacity-50"></div>
                        </div>

                        {{-- DESKRIPSI --}}
                        <p class="text-xs text-[var(--contrast-main-text)] leading-relaxed pr-6" x-text="cpl.deskripsi">
                        </p>
                    </div>

                </div>
            </template>
        </div>
    </div>
</div>
