<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Pengguna</h2>
    <div class="ml-auto">
        <flux:dropdown >
            <flux:button variant="primary" icon="plus" class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]"
                wire:target="addUser">
                Tambah Pengguna
            </flux:button>

            <flux:menu class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
                <flux:menu.heading>Pilih Role Pengguna</flux:menu.heading>
                <flux:menu.separator />

                {{-- Admin --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('admin');
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-red-700 dark:text-red-400');
                        $flux.modal('user-modal').show();
                        $wire.addUser('admin');
                    "
                    class="cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30">
                    <flux:icon name="cog-6-tooth" class="!text-red-700 dark:!text-red-400 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Admin</span>
                        <flux:icon wire:loading wire:target="addUser('admin')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Dosen --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('dosen');
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-lime-700 dark:text-lime-400');
                        $flux.modal('user-modal').show();
                        $wire.addUser('dosen');
                    "
                    class="cursor-pointer !text-lime-600 dark:!text-lime-400 hover:!bg-lime-50 dark:hover:!bg-lime-900/30">
                    <flux:icon name="briefcase" class="!text-lime-600 dark:!text-lime-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Dosen</span>
                        <flux:icon wire:loading wire:target="addUser('dosen')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Mahasiswa --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('mahasiswa');
                        $store.config?.setEdit(0);
                        $store.config?.resetSelect();
                        $store.config?.setColor('text-cyan-700 dark:text-cyan-400');
                        $flux.modal('user-modal').show();
                        $wire.addUser('mahasiswa');
                    "
                    class="cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-50 dark:hover:!bg-cyan-900/30">
                    <flux:icon name="book-open" class="!text-cyan-600 dark:!text-cyan-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Mahasiswa</span>
                        <flux:icon wire:loading wire:target="addUser('mahasiswa')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Input File --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('file');
                        $store.config?.setEdit(0);
                        $store.config?.setColor('text-green-700 dark:text-green-400');
                        $flux.modal('user-modal').show();
                        $wire.addUser('file');
                    "
                    class="cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-50 dark:hover:!bg-green-900/30">
                    <flux:icon name="table-cells" class="!text-green-600 dark:!text-green-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Input File Excel</span>
                        <flux:icon wire:loading wire:target="addUser('file')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>