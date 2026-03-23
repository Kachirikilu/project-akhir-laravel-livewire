// import Alpine from "alpinejs";

// window.Alpine = Alpine;

// Alpine.store("config", {
//     typeModal: "admin",
//     isEdit: 0,
//     colorIcon: "text-gray-700",

//     email: "",
//     password: "",
//     name: "",
//     nip: "",
//     nitk: "",
//     nidn: "",
//     nidk: "",
//     nim: "",
//     tahun_angkatan: "",
//     status: "",
//     prodi_id: "",
//     nama_prodi: "",

//     setType(val) {
//         this.typeModal = val;
//     },

//     setEdit(val) {
//         this.isEdit = val;
//     },

//     setColor(val) {
//         this.colorIcon = val;
//     },

//     setValueUser(
//         email,
//         password,
//         name,
//         nip,
//         nitk,
//         nidn,
//         nidk,
//         nim,
//         tahunAngkatan,
//         status,
//         idProdi,
//         namaProdi,
//     ) {
//         this.email = email;
//         this.password = password;
//         this.name = name;
//         this.nip = nip;
//         this.nitk = nitk;
//         this.nidn = nidn;
//         this.nidk = nidk;
//         this.nim = nim;
//         this.tahun_angkatan = tahunAngkatan;
//         this.status = status;
//         this.prodi_id = idProdi;
//         this.nama_prodi = namaProdi;
//     },

//     setDeleteUser(val) {
//         this.email = val;
//     },

//     reset() {
//         this.typeModal = "admin";
//         this.isEdit = 0;
//         this.colorIcon = "text-gray-700";

//         this.email = "";
//         this.password = "";
//         this.name = "";
//         this.nip = "";
//         this.nitk = "";
//         this.nidn = "";
//         this.nidk = "";
//         this.nim = "";
//         this.tahun_angkatan = "";
//         this.status = "";
//         this.prodi_id = "";
//         this.nama_prodi = "";
//     },
// });

// Alpine.start();
document.addEventListener("alpine:init", () => {
    Alpine.store("config", {
        typeModal: "",
        typeModal_2: "",
        isEdit: 0,
        colorIcon: "",

        email: "",
        email_2: "",
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
        nama_prodi_2: "",

        nama_strata: "",
        jurusan_id: "",
        nama_jurusan: "",
        nama_jurusan_2: "",
        fakultas_id: "",
        nama_fakultas: "",
        nama_fakultas_2: "",

        kode_pr: "",
        kode_jr: "",
        kode_fk: "",

        selected_kode_pr: "",
        selected_kode_jr: "",
        selected_kode_fk: "",

        nama_matkul: "",
        digit_semester: "",
        digit_mk: "",
        semester: "",
        kode_blok: "",
        tipe_sks: "",
        sks_kuliah: "",

        nama_prodi_array: [],
        prodi_id_array: [],
        selected_kode_pr_array: [],


        setType(val) {
            this.typeModal = val;
        },

        setEdit(val) {
            this.isEdit = val;
        },

        setColor(val) {
            this.colorIcon = val;
        },

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
            this.prodi_id = idProdi;
            this.nama_prodi = namaProdi;

            this.selected_kode_pr = kodePr;

        },

        setValueProdi(
            namaProdi,
            strata,
            idJurusan,
            namaJurusan,
            idFakultas,
            namaFakultas,
            kodePr,
            kodeJr,
            kodeFk
        ) {
            this.nama_prodi = namaProdi;
            this.nama_strata = strata;
            this.jurusan_id = idJurusan;
            this.nama_jurusan = namaJurusan;
            this.fakultas_id = idFakultas;
            this.nama_fakultas = namaFakultas;

            this.kode_pr = kodePr;
            this.kode_jr = kodeJr;
            this.kode_fk = kodeFk;

            this.selected_kode_pr = kodePr;
            this.selected_kode_jr = kodeJr;
            this.selected_kode_fk = kodeFk;
        },

        setDeleteUser(val) {
            this.email_2 = val;
        },
        setDeleteProdi(
            namaProdi,
            namaJurusan,
            namaFakultas,
            type
        ) {
            this.nama_prodi_2 = namaProdi;
            this.nama_jurusan_2 = namaJurusan;
            this.nama_fakultas_2 = namaFakultas;
            this.typeModal_2 = type;
        },

        resetSelect() {
            this.status = "";
            this.nama_strata = "";
            this.prodi_id = "";
            this.nama_prodi = "";
            this.jurusan_id = "";
            this.nama_jurusan = "";
            this.fakultas_id = "";
            this.nama_fakultas = "";
            
            this.selected_kode_pr = "";
            this.selected_kode_jr = "";
            this.selected_kode_fk = "";
        },

        reset() {
            this.typeModal = "";
            this.typeModal_2 = "";
            this.isEdit = 0;
            this.colorIcon = "";

            this.email = "";
            this.email_2 = "";
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
            this.nama_prodi_2 = "";

            this.nama_strata = "";
            this.jurusan_id = "";
            this.nama_jurusan = "";
            this.nama_jurusan_2 = "";
            this.fakultas_id = "";
            this.nama_fakultas = "";
            this.nama_fakultas_2 = "";

            this.kode_pr = "",
            this.kode_jr = "",
            this.kode_fk = "",

            this.selected_kode_pr = "",
            this.selected_kode_jr = "",
            this.selected_kode_fk = "",

            this.nama_matkul = "",
            this.digit_semester = "",
            this.digit_mk = "",
            this.semester = "",
            this.kode_blok = "",
            this.tipe_sks = "",
            this.sks_kuliah = "",

            this.nama_prodi_array = [],
            this.prodi_id_array = [],
            this.selected_kode_pr_array = []
        },
    });
});