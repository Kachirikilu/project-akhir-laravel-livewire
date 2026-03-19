<div class="flex flex-wrap items-center gap-2 mb-4">
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" class="bg-indigo-600 hover:bg-indigo-700"
                wire:target="showAddModal" wire:loading.attr="disabled">
                Tambah Pengguna
            </flux:button>

            <flux:menu class="min-w-48">
                <flux:menu.heading>Pilih Role Pengguna</flux:menu.heading>
                <flux:menu.separator />

                <flux:menu.item wire:click="showAddModal('admin')" class="!text-red-600 hover:!bg-red-50">
                    <flux:icon name="cog-6-tooth" class="!text-red-600 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Admin</span>
                        <flux:icon wire:loading wire:target="showAddModal('admin')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.item wire:click="showAddModal('dosen')" class="!text-lime-600 hover:!bg-lime-100">
                    <flux:icon name="briefcase" class="!text-lime-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Dosen</span>
                        <flux:icon wire:loading wire:target="showAddModal('dosen')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.item wire:click="showAddModal('mahasiswa')" class="!text-cyan-600 hover:!bg-cyan-50">
                    <flux:icon name="book-open" class="!text-cyan-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Mahasiswa</span>
                        <flux:icon wire:loading wire:target="showAddModal('mahasiswa')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item wire:click="showAddModal('file')" class="!text-green-600 hover:!bg-green-50">
                    <flux:icon name="table-cells" class="!text-green-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Input File Excel</span>
                        <flux:icon wire:loading wire:target="showAddModal('file')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>
