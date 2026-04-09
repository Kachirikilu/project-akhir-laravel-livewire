document.addEventListener("alpine:init", () => {
    Alpine.store("user", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        email_delete: "",
        
        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        // User
        email: "",
        password: "",

        // Admin, Dosen, Mahasiswa
        name: "",
        nip: "",
        nitk: "",
        nidn: "",
        nidk: "",
        nim: "",
        tahun_angkatan: "",
        status: "",

        // Prodi
        prodi_id: "",
        nama_prodi_search: "",
        prodi_items: "",

        setValueUser(
            email,
            password,
            name,
            nip,
            nitk,
            nidn,
            nidk,
            nim,
            tahunAngkatan,
            status,
            idProdi,
            namaProdi,
            namaJurusan,
            namaFakultas,
            kodePr
        ) {
            this.email = email;
            this.password = password;

            this.name = name;
            this.nip = nip;
            this.nitk = nitk;
            this.nidn = nidn;
            this.nidk = nidk;
            this.nim = nim;
            this.tahun_angkatan = tahunAngkatan;
            this.status = status;

            this.nama_prodi_search = namaProdi;
            this.prodi_items = {
                "id": idProdi,
                "kode": kodePr,
                "name": namaProdi,
                "name2": namaJurusan,
                "name3": namaFakultas
            };
        },
        setDeleteUser(email, forceDelete) {
            this.email_delete = email;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.status = "";
        // },
        reset() {
            this.typeModal = "",
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.email = "",
            this.password = "",

            // Admin, Dosen, Mahasiswa
            this.name = "",
            this.nip = "",
            this.nitk = "",
            this.nidn = "",
            this.nidk = "",
            this.nim = "",
            this.tahun_angkatan = "",
            this.status = "",

            // Prodi
            this.prodi_id = "",
            this.nama_prodi_search = "",
            this.prodi_items = "",

            // Delete
            this.email_delete = ""
        }
    });
});