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
        prodi_id: "",

        nama_prodi: "",
        nama_strata: "",
        jurusan_id: "",
        nama_jurusan: "",

        fakultas_id: "",
        nama_fakultas: "",

        nama_prodi_delete: "",
        nama_jurusan_delete: "",
        nama_fakultas_delete: "",

        kode_pr: "",
        kode_jr: "",
        kode_fk: "",
        kode_pr_delete: "",

        prodi_kode: "",
        jurusan_kode: "",
        fakultas_kode: "",

        nama_matkul: "",
        nama_matkul_delete: "",
        kode_mk_delete: "",
        digit_semester: "",
        digit_mk: "",
        semester: "",
        kode_blok: "",
        tipe_sks: "",
        sks_kuliah: "",

        nama_prodi_array: [],
        prodi_id_array: [],
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
            namaMatkul,
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
            this.nama_matkul = namaMatkul;

            this.kode_blok = kodeBlok;
            this.digit_semester = digitSemester;
            this.digit_mk = digitMk;

            this.nama_prodi = namaProdi;
            this.prodi_id = idProdi;
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
            this.nama_prodi_delete = namaProdi;
            this.nama_jurusan_delete = namaJurusan;
            this.nama_fakultas_delete = namaFakultas;
            this.kode_pr_delete = kodePrDelete;
            this.typeModal_delete = type;
            this.isForceDelete = forceDelete;
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

        resetSelect() {
            this.email = "";
            this.password = "";
            
            this.status = "";
            this.nama_strata = "";
            this.prodi_id = "";
            // this.nama_prodi = "";
            this.nama_prodi_delete = "";
            this.jurusan_id = "";
            this.nama_jurusan = "";
            this.fakultas_id = "";
            this.nama_fakultas = "";
            
            this.prodi_kode = "";
            this.jurusan_kode = "";
            this.fakultas_kode = "";

            this.nama_matkul = "";
            this.kode_blok = "";

            this.semester = "";
            this.sks_kuliah = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";

            this.nama_prodi_array = [];
            this.prodi_id_array = [];
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
            this.prodi_id = "";

            this.nama_prodi = "";
            this.nama_prodi_delete = "";

            this.nama_strata = "";
            this.jurusan_id = "";
            this.nama_jurusan = "";
            this.nama_jurusan_delete = "";
            this.fakultas_id = "";
            this.nama_fakultas = "";
            this.nama_fakultas_delete = "";

            this.kode_pr = "";
            this.kode_jr = "";
            this.kode_fk = "";
            this.kode_pr_delete = ""

            this.prodi_kode = "";
            this.jurusan_kode = "";
            this.fakultas_kode = "";

            this.nama_matkul = "";
            this.nama_matkul_delete = "";
            this.kode_mk_delete = "";
            this.digit_semester = "";
            this.digit_mk = "";
            
            this.kode_blok = "";

            this.semester = "";
            this.sks_kuliah = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";

            this.nama_prodi_array = [];
            this.prodi_id_array = [];
            this.prodi_kode_array = [];
        },
    });
});