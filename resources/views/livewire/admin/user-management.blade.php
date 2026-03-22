<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.admin.user-management.user-toolbar')
    @include('livewire.admin.user-management.user-search-and-filters')

    @include('livewire.admin.user-management.user-table')

    @include('livewire.admin.user-management.user-modal-form')
    @include('livewire.admin.user-management.user-modal-delete')
</div>

{{-- <script defer>
    document.addEventListener('alpine:init', () => {
        Alpine.store('config', {
            typeModal: 'admin',
            isEdit: 0,
            colorIcon: 'text-gray-700',

            email: '',
            password: '',
            name: '',
            nip: '',
            nitk: '',
            nidn: '',
            nidk: '',
            nim: '',
            tahun_angkatan: '',
            status: '',
            prodi_id: '',
            nama_prodi: '',

            setType(val) {
                this.typeModal = val
            },

            setEdit(val) {
                this.isEdit = val
            },

            setColor(val) {
                this.colorIcon = val
            },

            setValueUser(email, password, name, nip, nitk, nidn, nidk, nim, tahunAngkatan, status,
                idProdi, namaProdi) {
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
                this.prodi_id = idProdi
                this.nama_prodi = namaProdi
            },

            setDeleteUser(val) {
                this.email = val
            },

            reset() {
                this.typeModal = 'admin'
                this.isEdit = 0
                this.colorIcon = 'text-gray-700'

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
                this.prodi_id = ''
                this.nama_prodi = ''
            }
        })
    })
</script> --}}
