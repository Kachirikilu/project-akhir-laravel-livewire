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

                {{-- Jurusan --}}
                <flux:menu.item
                    @click="
                        $store.config.setType('jurusan');
                        $store.config.setEdit(0);
                        $store.config.setColor('text-lime-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('jurusan');
                    "
                    class="cursor-pointer !text-lime-600 hover:!bg-lime-100">
                    <flux:icon name="book-open" class="!text-lime-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Jurusan</span>
                        <flux:icon wire:loading wire:target="addProdi('jurusan')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>

                {{-- Fakultas --}}
                <flux:menu.item
                    @click="
                        $store.config.setType('fakultas');
                        $store.config.setEdit(0);
                        $store.config.setColor('text-cyan-700');
                        $flux.modal('prodi-modal').show();
                        $wire.addProdi('fakultas');
                    "
                    class="cursor-pointer !text-cyan-600 hover:!bg-cyan-50">
                    <flux:icon name="building-library" class="!text-cyan-600 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Fakultas</span>
                        <flux:icon wire:loading wire:target="addProdi('fakultas')" name="arrow-path"
                            class="animate-spin h-4 w-4" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('config', {
                typeModal: 'prodi',
                isEdit: 0,
                colorIcon: 'text-red-700',

                nama_prodi: '',
                nama_strata: '',
                jurusan_id: '',
                nama_jurusan: '',
                fakultas_id: '',
                nama_fakultas: '',

                setType(val) {
                    this.typeModal = val
                },

                setEdit(val) {
                    this.isEdit = val
                },

                setColor(val) {
                    this.colorIcon = val
                },

                setValueProdi(namaProdi, strata, idJurusan, namaJurusan, idFakultas, namaFakultas) {
                    this.nama_prodi = namaProdi
                    this.nama_strata = strata
                    this.jurusan_id = idJurusan
                    this.nama_jurusan = namaJurusan
                    this.fakultas_id = idFakultas
                    this.nama_fakultas = namaFakultas
                },

                reset() {
                    this.typeModal = ''
                    this.isEdit = 0
                    this.colorIcon = 'text-gray-700'

                    this.nama_prodi = ''
                    this.nama_strata = ''
                    this.jurusan_id = ''
                    this.nama_jurusan = ''
                    this.fakultas_id = ''
                    this.nama_fakultas = ''
                }
            })
        })
    </script>
</div>
