<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    {{-- <h2 class="text-2xl font-bold mb-4 text-gray-700">Manajemen Program Studi</h2> --}}
    @include('livewire.admin.prodi-management.prodi-toolbar')
    @include('livewire.admin.prodi-management.prodi-switch-table')

    @include('livewire.admin.prodi-management.prodi-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.prodi-management.prodi-table', [
            'xResults' => match ($this->switchTable) {
                'prodi' => $prodis,
                'jurusan' => $jurusans,
                'fakultas' => $fakultass,
                default => collect([]),
            },
            'xNameString' => match ($this->switchTable) {
                'prodi' => 'Program Studi',
                'jurusan' => 'Jurusan',
                'fakultas' => 'Fakultas',
                default => 'Data',
            },
        ])
    </div>

    @include('livewire.admin.prodi-management.prodi-modal-form')
    @include('livewire.admin.prodi-management.prodi-modal-delete')
</div>

{{-- <script defer>
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
                this.typeModal = 'prodi'
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
</script> --}}
