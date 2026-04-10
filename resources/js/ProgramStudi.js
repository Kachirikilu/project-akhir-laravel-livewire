document.addEventListener("alpine:init", () => {
    Alpine.store("prodi", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_pr_delete: "",
        nama_jr_delete: "",
        nama_fk_delete: "",
        kode_delete: "",
        
        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        // Prodi
        nama_pr: "",
        jr_id: "",
        nama_jr_search: "",
        kodePr: "",
        strata: "",

        // Jurusan
        nama_jr: "",
        jr_id: "",
        nama_fakulas_search: "",
        kodeJr: "",
        strata: "",

        // Fakultas
        nama_fk: "",
        kodeFk: "",

        // Items
        jurusanItems: "",
        fakultasItems: "",

        setValueProdi(
            namaProdi,
            strata,
            idJurusan,
            jurusan,
            idFakultas,
            fakultas,
            kodePr,
            kodeJr,
            kodeFk,
        ) {
            this.nama_pr = namaProdi;
            this.jr_id = idJurusan;
            this.nama_jr_search = jurusan;
            this.kode_pr = kodePr;
            this.strata = strata;

            this.nama_jr = jurusan;
            this.fk_id = idFakultas;
            this.nama_fk_search = fakultas;
            this.kode_jr = kodePr;

            this.nama_fk = fakultas;
            this.kode_fk = kodePr;

            this.jr_items = {
                "kode": kodeJr,
                "slot1": jurusan,
                "slot2": fakultas,
            };

            this.fk_items = {
                "kode": kodeFk,
                "slot1": fakultas,
            };
        },

        setDeleteProdi(
            namaProdi,
            jurusan,
            fakultas,
            kodePrDelete,
            type,
            forceDelete
        ) {
            this.nama_pr_delete = namaProdi;
            this.nama_jr_delete = jurusan;
            this.nama_fk_delete = fakultas;
            this.kode_delete = kodePrDelete;
            this.typeModal_delete = type;
            this.isForceDelete = forceDelete;
        },

        resetSelect() {
            this.strata = "";
        },
        
        reset() {
            this.typeModal = "",
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.nama_pr = "";
            this.jr_id = "";
            this.nama_jr_search = "";
            this.kode_pr = "";
            this.strata = "";

            this.nama_jr = "";
            this.fk_id = "";
            this.nama_fk_search = "";
            this.kode_jr = "";

            this.nama_fk = "";
            this.kode_fk = "";

            this.jr_items = "";
            this.fk_items = "";

            this.nama_pr_delete = "",
            this.nama_jr_delete = "",
            this.nama_fk_delete = "",
            this.kode_delete = ""
        }
    });
});