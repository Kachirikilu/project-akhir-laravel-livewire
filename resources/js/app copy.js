const savedTheme = localStorage.getItem('app-theme') || 'blue';
document.documentElement.setAttribute('data-theme', savedTheme);

document.addEventListener("alpine:init", () => {
    Alpine.store("config", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        email: "",
        email_delete: "",
        password: "",
        name: "",
        nip: "",
        nitk: "",
        nidn: "",
        nidk: "",
        nim: "",
        tahun_angkatan: "",
        status: "",
        pr_id: "",

        nama_pr: "",
        strata: "",
        jr_id: "",
        nama_jr: "",

        fk_id: "",
        nama_fk: "",

        nama_pr_delete: "",
        nama_jr_delete: "",
        nama_fk_delete: "",

        kode_pr: "",
        kode_jr: "",
        kode_fk: "",
        kode_pr_delete: "",

        prodi_kode: "",
        jurusan_kode: "",
        fakultas_kode: "",

        nama_mk: "",
        nama_mk_delete: "",
        kode_mk_delete: "",
        digit_semester: "",
        digit_mk: "",
        semester: "",
        kode_blok: "",
        tipe_sks: "",
        sks_kuliah: "",

        nama_pr_array: [],
        pr_id_array: [],
        prodi_kode_array: [],

        is_wajib: "",


        setType(val) {
            this.typeModal = val;
        },

        setEdit(val) {
            this.isEdit = val;
        },

        setColor(val) {
            this.colorIcon = val;
        },

        setValueMK(
            tingkatanMode,
            namaMK,
            kodeBlok,
            digitSemester,
            digitMk,
            namaProdi,
            idProdi,
            kodePr,
            semester,
            sksKuliah,
            tipeSks,
            isWajib
        ) {
            this.typeModal = tingkatanMode;
            this.nama_mk = namaMK;

            this.kode_blok = kodeBlok;
            this.digit_semester = digitSemester;
            this.digit_mk = digitMk;

            this.nama_pr = namaProdi;
            this.pr_id = idProdi;
            this.kode_pr = kodePr;

            this.semester = semester;
            this.sks_kuliah = sksKuliah;
            this.tipe_sks = tipeSks; 
            this.is_wajib = isWajib;
        },


        setDeleteUser(val, forceDelete) {
            this.email_delete = val;
            this.isForceDelete = forceDelete;
        },
        setDeleteProdi(
            namaProdi,
            namaJurusan,
            namaFakultas,
            kodePrDelete,
            type,
            forceDelete
        ) {
            this.nama_pr_delete = namaProdi;
            this.nama_jr_delete = namaJurusan;
            this.nama_fk_delete = namaFakultas;
            this.kode_pr_delete = kodePrDelete;
            this.typeModal_delete = type;
            this.isForceDelete = forceDelete;
        },
        setDeleteMK(
            namaProdi,
            kodeMkDelete,
            forceDelete
        ) {
            this.nama_mk_delete = namaProdi;
            this.kode_mk_delete = kodeMkDelete;
            this.isForceDelete = forceDelete;
        },

        resetSelect() {
            this.email = "";
            this.password = "";
            
            this.status = "";
            this.strata = "";
            this.pr_id = "";
            // this.nama_pr = "";
            this.nama_pr_delete = "";
            this.jr_id = "";
            this.nama_jr = "";
            this.fk_id = "";
            this.nama_fk = "";
            
            this.prodi_kode = "";
            this.jurusan_kode = "";
            this.fakultas_kode = "";

            this.nama_mk = "";
            this.kode_blok = "";

            this.semester = "";
            this.sks_kuliah = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";

            this.nama_pr_array = [];
            this.pr_id_array = [];
            this.prodi_kode_array = [];
        },
        // resetForceDelete() {
        //     this.isForceDelete = 0;
        // },
        reset() {
            this.typeModal = "";
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

            this.email = "";
            this.email_delete = "";
            this.password = "";
            this.name = "";
            this.nip = "";
            this.nitk = "";
            this.nidn = "";
            this.nidk = "";
            this.nim = "";
            this.tahun_angkatan = "";
            this.status = "";
            this.pr_id = "";

            this.nama_pr = "";
            this.nama_pr_delete = "";

            this.strata = "";
            this.jr_id = "";
            this.nama_jr = "";
            this.nama_jr_delete = "";
            this.fk_id = "";
            this.nama_fk = "";
            this.nama_fk_delete = "";

            this.kode_pr = "";
            this.kode_jr = "";
            this.kode_fk = "";
            this.kode_pr_delete = ""

            this.prodi_kode = "";
            this.jurusan_kode = "";
            this.fakultas_kode = "";

            this.nama_mk = "";
            this.nama_mk_delete = "";
            this.kode_mk_delete = "";
            this.digit_semester = "";
            this.digit_mk = "";
            
            this.kode_blok = "";

            this.semester = "";
            this.sks_kuliah = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";

            this.nama_pr_array = [];
            this.pr_id_array = [];
            this.prodi_kode_array = [];
        },
    });
});