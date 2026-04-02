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
                <flux:menu.heading>Pilih Tingkatan</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.rps?.setType();
                        $store.rps?.setEdit(0);
                        $store.rps?.resetSelect();
                        $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
                        $flux.modal('rps-modal').show();
                        $wire.addRPS();
                    "
                    class="cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Rencana Pembelajaran Semester</span>
                        <flux:icon wire:loading wire:target="addRPS()" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Jurusan --}}
                {{-- <flux:menu.item
                    @click="
                        $store.rps?.setType('rps-jurusan');
                        $store.rps?.setEdit(0);
                        $store.rps?.resetSelect();
                        $store.rps?.setColor('text-amber-700 dark:text-amber-400');
                        $flux.modal('rps-modal').show();
                        $wire.addRPS('rps-jurusan');
                    "
                    class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                    <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Jurusan</span>
                        <flux:icon wire:loading wire:target="addRPS('jurusan')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item> --}}

                {{-- Fakultas --}}
                {{-- <flux:menu.item
                    @click="
                        $store.rps?.setType('rps-fakultas');
                        $store.rps?.setEdit(0);
                        $store.rps?.resetSelect();
                        $store.rps?.setColor('text-[var(--focus-color)]');
                        $flux.modal('rps-modal').show();
                        $wire.addRPS('rps-fakultas');
                    "
                    class="cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-50 dark:hover:!bg-indigo-900/30">
                    <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Fakultas</span>
                        <flux:icon wire:loading wire:target="addRPS('fakultas')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item> --}}

                {{-- Universitas --}}
                {{-- <flux:menu.item
                    @click="
                        $store.rps?.setType('rps-universitas');
                        $store.rps?.setEdit(0);
                        $store.rps?.resetSelect();
                        $store.rps?.setColor('text-[var(--focus-color)]');
                        $flux.modal('rps-modal').show();
                        $wire.addRPS('rps-universitas');
                    "
                    class="cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30">
                    <flux:icon name="building-library" class="!text-yellow-600 dark:!text-yellow-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Universitas</span>
                        <flux:icon wire:loading wire:target="addRPS('universitas')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item> --}}
            </flux:menu>
        </flux:dropdown>
    </div>
</div>