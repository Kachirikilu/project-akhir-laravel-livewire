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
        angkatan: "",
        status: "",

        // Prodi
        pr_id: "",
        nama_pr_search: "",
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
            angkatan,
            status,
            idProdi,
            kodePr,
            prodi,
            jurusan,
            fakultas
        ) {
            this.email = email;
            this.password = password;

            this.name = name;
            this.nip = nip;
            this.nitk = nitk;
            this.nidn = nidn;
            this.nidk = nidk;
            this.nim = nim;
            this.angkatan = angkatan;
            this.status = status;

            this.pr_id = idProdi,
            this.nama_pr_search = prodi;
            this.prodi_items = {
                "id": idProdi,
                "kode": kodePr,
                "name": prodi,
                "name2": jurusan,
                "name3": fakultas
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
            this.angkatan = "",
            this.status = "",

            // Prodi
            this.pr_id = "",
            this.nama_pr_search = "",
            this.prodi_items = "",

            // Delete
            this.email_delete = ""
        }
    });
});