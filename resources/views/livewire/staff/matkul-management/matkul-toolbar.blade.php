<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Mata Kuliah</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" 
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]"
                wire:target="addMK">
                Tambah Mata Kuliah
            </flux:button>

            <flux:menu class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
                <flux:menu.heading>Pilih Tingkatan</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType(1);
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-emerald-700 dark:text-emerald-400');
                        $flux.modal('mk-modal').show();
                        $wire.addMK(1);
                    "
                    class="cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Program Studi</span>
                        <flux:icon wire:loading wire:target="addMK(1)" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Jurusan --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType(2);
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-amber-700 dark:text-amber-400');
                        $flux.modal('mk-modal').show();
                        $wire.addMK(2);
                    "
                    class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                    <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Jurusan</span>
                        <flux:icon wire:loading wire:target="addMK(2)" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Fakultas --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType(3);
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-indigo-700 dark:text-indigo-400');
                        $flux.modal('mk-modal').show();
                        $wire.addMK(3);
                    "
                    class="cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-50 dark:hover:!bg-indigo-900/30">
                    <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Fakultas</span>
                        <flux:icon wire:loading wire:target="addMK(3)" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Universitas --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType(4);
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-red-700 dark:text-red-400');
                        $flux.modal('mk-modal').show();
                        $wire.addMK(4);
                    "
                    class="cursor-pointer !text-red-600 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30">
                    <flux:icon name="globe-alt" class="!text-red-600 dark:!text-red-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Universitas</span>
                        <flux:icon wire:loading wire:target="addMK(4)" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>