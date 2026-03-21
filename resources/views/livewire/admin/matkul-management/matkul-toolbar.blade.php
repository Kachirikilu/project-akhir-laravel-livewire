<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-700 dark:text-gray-200">Manajemen Program Studi</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" 
                class="cursor-pointer text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                wire:target="addProdi">
                Tambah Mata Kuliah
            </flux:button>

            <flux:menu class="min-w-48 dark:bg-neutral-800 dark:border-neutral-700">
                <flux:menu.heading class="dark:text-gray-400">Pilih Tingkatan</flux:menu.heading>
                <flux:menu.separator class="dark:border-neutral-700" />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('prodi');
                        $store.config?.setEdit(0);
                        $store.config?.setColor('text-emerald-700 dark:text-emerald-400');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('prodi');
                    "
                    class="cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Program Studi</span>
                        <flux:icon wire:loading wire:target="addProdi('prodi')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Jurusan --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('jurusan');
                        $store.config?.setEdit(0);
                        $store.config?.setColor('text-amber-700 dark:text-amber-400');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('jurusan');
                    "
                    class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                    <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Jurusan</span>
                        <flux:icon wire:loading wire:target="addProdi('jurusan')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Fakultas --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('fakultas');
                        $store.config?.setEdit(0);
                        $store.config?.setColor('text-indigo-700 dark:text-indigo-400');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('fakultas');
                    "
                    class="cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-50 dark:hover:!bg-indigo-900/30">
                    <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Fakultas</span>
                        <flux:icon wire:loading wire:target="addProdi('fakultas')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>