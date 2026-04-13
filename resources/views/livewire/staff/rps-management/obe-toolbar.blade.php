<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Rencana Pembelajaran Semester</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" 
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]"
                wire:target="addRPS">
                Tambah OBE
            </flux:button>

            <flux:menu class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
                <flux:menu.heading>Pilih OBE</flux:menu.heading>
                <flux:menu.separator />

                @if ($typeXString == 'all')
                    {{-- RPS --}}
                    <flux:menu.item
                        @click="
                            $store.rps?.setType();
                            $store.rps?.setEdit(0);
                            $store.rps?.setFlyout(false);
                            $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
                            $flux.modal('rps-modal').show();
                            $wire.addRPS();
                        "
                        class="cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30">
                        <flux:icon name="clipboard-document-list" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span>Rencana Pembelajaran Semester</span>
                            <flux:icon wire:loading wire:target="addRPS()" name="arrow-path"
                                class="animate-spin h-4 w-4" />
                        </div>
                    </flux:menu.item>

                @endif

                @if ($typeXString == 'cpmk' || $typeXString == 'all')
                    {{-- CPMK --}}
                    <flux:menu.item
                        @click="
                            $store.cpmk?.setType();
                            $store.cpmk?.setEdit(0);
                            $store.cpmk?.setFlyout({{ $isFlyout }});
                            $store.cpmk?.setColor('text-amber-700 dark:text-amber-400');
                            $flux.modal('cpmk-modal').show();
                            $wire.addCPMK();
                        "
                        class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-50 dark:hover:!bg-amber-900/30">
                        <flux:icon name="academic-cap" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span>Capaian Pembelajaran Mata Kuliah</span>
                            <flux:icon wire:loading wire:target="addRPS()" name="arrow-path"
                                class="animate-spin h-4 w-4" />
                        </div>
                    </flux:menu.item>
                @endif


            </flux:menu>
        </flux:dropdown>
    </div>
</div>