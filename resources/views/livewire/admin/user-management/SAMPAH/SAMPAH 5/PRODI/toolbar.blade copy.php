<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" class="cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                wire:target="addProdi" wire:loading.attr="disabled">
                Tambah Program Studi
            </flux:button>

            <flux:menu class="min-w-48">
                <flux:menu.heading>Pilih Jenis</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item 
                    {{-- @click="$wire.addProdi('prodi'); $flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'prodi' }); " --}}
                    {{-- @click="$flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'prodi' }); $wire.addProdi('prodi');" --}}
                    @click="
                        {{-- pType = 'prodi'; --}}
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('prodi');
                        $store.config.setType('prodi')
                    "
                    class="cursor-pointer !text-red-600 hover:!bg-red-50">
                    <flux:icon name="cog-6-tooth" class="!text-red-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Program Studi</span>
                        <flux:icon wire:loading wire:target="addProdi('prodi')" name="arrow-path" class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Jurusan --}}
                <flux:menu.item 
                    {{-- @click="$wire.addProdi('jurusan'); $flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'jurusan' }); " --}}
                    {{-- @click="$flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'jurusan' }); $wire.addProdi('jurusan');" --}}
                    @click="
                        {{-- pType = 'jurusan'; --}}
                        $flux.modal('jurusan-modal').show();
                        $wire.addProdi('jurusan');
                        $store.config.setType('jurusan')
                    "
                    class="cursor-pointer !text-lime-600 hover:!bg-lime-100">
                    <flux:icon name="briefcase" class="!text-lime-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Jurusan</span>
                        <flux:icon wire:loading
                        {{-- wire:target="addProdi('jurusan')" --}}
                        name="arrow-path" class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Fakultas --}}
                <flux:menu.item 
                    {{-- @click="$wire.addProdi('fakultas'); $flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'fakultas' }); " --}}
                    {{-- @click="$flux.modal('prodi-modal').show(); $dispatch('setup-modal', { prodiType: 'fakultas' }); $wire.addProdi('fakultas');" --}}
                    @click="
                        {{-- pType = 'fakultas'; --}}
                        $flux.modal('fakultas-modal').show();
                        $wire.addProdi('fakultas');
                        $store.config.setType('fakultas')
                    "
                    class="cursor-pointer !text-cyan-600 hover:!bg-cyan-50">
                    <flux:icon name="book-open" class="!text-cyan-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Fakultas</span>
                        <flux:icon wire:loading wire:target="addProdi('fakultas')" name="arrow-path" class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <script>
        document.addEventListener('alpine:init',
            () => {
                Alpine.store('config', {
                    type: 'prodi',
                    setType(val) { this.type = val }
                })
            }
        )
    </script>
</div>