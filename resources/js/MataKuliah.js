document.addEventListener("alpine:init", () => {
    Alpine.store("mk", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_matkul_delete: "",
        kode_mk_delete: "",
        
        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        nama_matkul: "",
        digit_semester: "",
        digit_mk: "",
        semester: "",
        kode_blok: "",
        tipe_sks: "",
        sks_kuliah: "",
        is_wajib: "",

        // nama_prodi_search_array: [],
        // prodi_id: "",
        // prodi_items: "",
        // prodi_id_array: [],
        // prodi_items_array: [],

    // init() {
    //     // Listener untuk mengisi store dari Livewire
    //     window.addEventListener('fill-store-mk', (event) => {
    //         this.isEdit = event.detail.isEdit;
    //         this.prodi_id = event.detail.prodi_id;
    //         this.nama_prodi = event.detail.nama_prodi;
    //         this.prodi_items = event.detail.prodi_items;
    //     });
    // },

        setValueMK(
            tingkatanMode,
            namaMatkul,
            kodeBlok,
            digitSemester,
            digitMk,
            // idProdi,
            // kodePr,
            // namaProdi,
            // namaJurusan,
            // namaFakultas,
            semester,
            sksKuliah,
            tipeSks,
            isWajib
        ) {
            this.typeModal = tingkatanMode;
            this.nama_matkul = namaMatkul;

            this.kode_blok = kodeBlok;
            this.digit_semester = digitSemester;
            this.digit_mk = digitMk;

            this.semester = semester;
            this.sks_kuliah = sksKuliah;
            this.tipe_sks = tipeSks; 
            this.is_wajib = isWajib;

            // this.nama_prodi_search = namaProdi;
            // this.prodi_id = idProdi;
            // this.prodi_items = kodePr;
            // this.prodi_items = {
            //     "kode": kodePr,
            //     "name": namaProdi,
            //     // "name2": namaJurusan,
            //     // "name3": namaFakultas,
            // };
        },

        setDeleteMK(
            namaProdi,
            kodeMkDelete,
            forceDelete
        ) {
            this.nama_matkul_delete = namaProdi;
            this.kode_mk_delete = kodeMkDelete;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.kode_blok = "";
        //     this.semester = "";
        //     this.tipe_sks = ""; 
        //     this.is_wajib = "";
        // },
        
        reset() {
            this.typeModal = "",
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.typeModal = "";
            this.nama_matkul = "";

            this.kode_blok = "";
            this.digit_semester = "";
            this.digit_mk = "";

            this.semester = "";
            this.sks_kuliah = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";

            this.nama_matkul_delete = "";
            this.kode_mk_delete = "";

            // this.nama_prodi_search = "";
            // this.prodi_id = "";
            // this.prodi_items = "";
            // this.nama_prodi_array = [];
            // this.prodi_id_array = [];
            // this.prodi_items_array = [];
        }
    });
});