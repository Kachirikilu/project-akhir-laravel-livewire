<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" class="cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                wire:target="addProdi">
                Tambah Program Studi
            </flux:button>

            <flux:menu class="min-w-48">
                <flux:menu.heading>Pilih Jenis</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.config?.setType('prodi');
                        $store.config?.setEdit(0);
                        $store.config?.setColor('text-emerald-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('prodi');
                    "
                    class="cursor-pointer !text-emerald-600 hover:!bg-emerald-50">
                    <flux:icon name="academic-cap" class="!text-emerald-600 mr-2 h-4 w-4" />
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
                        $store.config?.setColor('text-amber-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('jurusan');
                    "
                    class="cursor-pointer !text-amber-600 hover:!bg-amber-100">
                    <flux:icon name="book-open" class="!text-amber-600 mr-2 h-4 w-4" />
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
                        $store.config?.setColor('text-indigo-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('fakultas');
                    "
                    class="cursor-pointer !text-indigo-600 hover:!bg-indigo-50">
                    <flux:icon name="building-library" class="!text-indigo-600 mr-2 h-4 w-4" />
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
