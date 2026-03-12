<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Pengguna</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" class="cursor-pointer bg-indigo-600 hover:bg-indigo-700"
                wire:target="addUser" wire:loading.attr="disabled">
                Tambah Pengguna
            </flux:button>

            <flux:menu class="min-w-48">
                <flux:menu.heading>Pilih Role Pengguna</flux:menu.heading>
                <flux:menu.separator />

                {{-- Admin --}}
                <flux:menu.item wire:click="addUser('admin')" class="cursor-pointer !text-red-600 hover:!bg-red-50">
                    <flux:icon name="cog-6-tooth" class="!text-red-600 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Admin</span>
                        <flux:icon wire:loading wire:target="addUser('admin')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.item
                    @click="
                        $store.config.setType('prodi');
                        $store.config.setEdit(0);
                        $store.config.setColor('text-red-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('prodi');
                    "
                    class="cursor-pointer !text-red-600 hover:!bg-red-50">
                    <flux:icon name="academic-cap" class="!text-red-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Program Studi</span>
                        <flux:icon wire:loading wire:target="addProdi('prodi')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Dosen --}}
                <flux:menu.item wire:click="addUser('dosen')" class="cursor-pointer !text-lime-600 hover:!bg-lime-100">
                    <flux:icon name="briefcase" class="!text-lime-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Dosen</span>
                        <flux:icon wire:loading wire:target="addUser('dosen')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Mahasiswa --}}
                <flux:menu.item wire:click="addUser('mahasiswa')"
                    class="cursor-pointer !text-cyan-600 hover:!bg-cyan-50">
                    <flux:icon name="book-open" class="!text-cyan-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Mahasiswa</span>
                        <flux:icon wire:loading wire:target="addUser('mahasiswa')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item wire:click="addUser('file')" class="cursor-pointer !text-green-600 hover:!bg-green-50">
                    <flux:icon name="table-cells" class="!text-green-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Input File Excel</span>
                        <flux:icon wire:loading wire:target="addUser('file')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('config', {
                typeModal: 'admin',
                isEdit: 0,
                colorIcon: 'text-red-700',

                email = ''
                password = ''
                name = ''
                nip = ''
                nitk = ''
                nidn = ''
                nidk = ''
                nim = ''
                tahun_angkatan = ''
                status = ''

                setType(val) {
                    this.typeModal = val
                },

                setEdit(val) {
                    this.isEdit = val
                },

                setColor(val) {
                    this.colorIcon = val
                },

                setValueUser(email, password, name, nip, nitk, nidn, nidk, nim, tahunAngkatan, status) {
                    this.email = email
                    this.password = password
                    this.name = name
                    this.nip = nip
                    this.nitk = nitk
                    this.nidn = nidn
                    this.nidk = nidk
                    this.nim = nim
                    this.tahun_angkatan = tahunAngkatan
                    this.status = status
                },

                reset() {
                    this.typeModal = ''
                    this.isEdit = 0
                    this.colorIcon = ''

                    this.email = ''
                    this.password = ''
                    this.name = ''
                    this.nip = ''
                    this.nitk = ''
                    this.nidn = ''
                    this.nidk = ''
                    this.nim = ''
                    this.tahun_angkatan = ''
                    this.status = ''
                }
            })
        })
    </script>
</div>
