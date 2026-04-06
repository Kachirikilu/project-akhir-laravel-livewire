document.addEventListener("alpine:init", () => {
    Alpine.store("prodi", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_prodi_delete: "",
        nama_jurusan_delete: "",
        nama_fakultas_delete: "",
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
        nama_prodi: "",
        jurusan_id: "",
        nama_jurusan_search: "",
        kodePr: "",
        nama_strata: "",

        // Jurusan
        nama_jurusan: "",
        jurusan_id: "",
        nama_fakulas_search: "",
        kodeJr: "",
        nama_strata: "",

        // Fakultas
        nama_fakultas: "",
        kodeFk: "",

        // Items
        jurusanItems: "",
        fakultasItems: "",

        setValueProdi(
            namaProdi,
            strata,
            idJurusan,
            namaJurusan,
            idFakultas,
            namaFakultas,
            kodePr,
            kodeJr,
            kodeFk,
        ) {
            this.nama_prodi = namaProdi;
            this.jurusan_id = idJurusan;
            this.nama_jurusan_search = namaJurusan;
            this.kode_pr = kodePr;
            this.nama_strata = strata;

            this.nama_jurusan = namaJurusan;
            this.fakultas_id = idFakultas;
            this.nama_fakultas_search = namaFakultas;
            this.kode_jr = kodePr;

            this.nama_fakultas = namaFakultas;
            this.kode_fk = kodePr;

            this.jurusan_items = {
                "kode": kodeJr,
                "name": namaJurusan,
                "name2": namaFakultas,
            };

            this.fakultas_items = {
                "kode": kodeFk,
                "name": namaFakultas,
            };
        },

        setDeleteProdi(
            namaProdi,
            namaJurusan,
            namaFakultas,
            kodePrDelete,
            type,
            forceDelete
        ) {
            this.nama_prodi_delete = namaProdi;
            this.nama_jurusan_delete = namaJurusan;
            this.nama_fakultas_delete = namaFakultas;
            this.kode_delete = kodePrDelete;
            this.typeModal_delete = type;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.nama_strata = "";
        // },
        
        reset() {
            this.typeModal = "",
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.nama_prodi = "";
            this.jurusan_id = "";
            this.nama_jurusan_search = "";
            this.kode_pr = "";
            this.nama_strata = "";

            this.nama_jurusan = "";
            this.fakultas_id = "";
            this.nama_fakultas_search = "";
            this.kode_jr = "";

            this.nama_fakultas = "";
            this.kode_fk = "";

            this.jurusan_items = "";
            this.fakultas_items = "";

            this.nama_prodi_delete = "",
            this.nama_jurusan_delete = "",
            this.nama_fakultas_delete = "",
            this.kode_delete = ""
        }
    });
});