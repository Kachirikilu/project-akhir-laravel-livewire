document.addEventListener("alpine:init", () => {
    Alpine.store("rps", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        rps_delete: "",
        kode_rps_delete: "",
        
        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        matkul_id: "",
        tahun_akademik: "",
        tahun_akademik_1: "",
        tahun_akademik_2: "",
        is_draf: "",

        count_scpmk: 0,
        setCountSCPMK(val) {
            this.count_scpmk = val;

            if (val < 14 && (this.is_draf === 0 || this.is_draf === 1)) {
                this.is_draf = 1;
            } else if (val < 14 && this.is_draf === "") {
                this.is_draf = "";
            } 
        },


        ref_cpmk: [], 
        ref_scpmk: [],
        cpl_cpmk: [],

        // Di dalam Alpine.store('rps')
        update(allSubItems) {
            if (!allSubItems || allSubItems.length === 0) {
                this.ref_cpmk = [];
                this.ref_scpmk = [];
                this.cpl_cpmk = [];
                this.count_scpmk = 0;
                return;
            }

            this.ref_cpmk = [];
            this.ref_scpmk = [];
            this.cpl_cpmk = [];

            allSubItems.forEach(item => {
                if (item.cpl) {
                    const combinedCPL = [...this.cpl_cpmk, ...item.cpl];
                    this.cpl_cpmk = Array.from(new Map(combinedCPL.map(i => [i.id, i])).values());
                }
                if (item.ref) {
                    const combinedRef = [...this.ref_cpmk, ...item.ref];
                    this.ref_cpmk = Array.from(new Map(combinedRef.map(i => [i.id, i])).values());
                }
                if (item.scpmk) {
                    let rawSubRefs = item.scpmk.flatMap(sub => sub.ref || []);
                    const combinedSubRef = [...this.ref_scpmk, ...rawSubRefs];
                    this.ref_scpmk = Array.from(new Map(combinedSubRef.map(i => [i.id, i])).values());
                }
            });
        },


        setTypeModal(type) {
            this.typeModal = type;
        },

        setIsEdit(isEdit) {
            this.isEdit = isEdit;
        },

        // reset() {
        //     // this.items = [];
        //     // this.scpmkCount = 0;
        //     // this.typeModal = null;
        //     // this.isEdit = false;
        // },

        // nama_prodi_search_array: [],
        // prodi_id_array: [],
        // prodi_kode_array: [],

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

            this.semester = semester;
            this.sks_kuliah = sksKuliah;
            this.tipe_sks = tipeSks; 
            this.is_wajib = isWajib;

            this.nama_prodi_search = namaProdi;
            this.prodi_id = idProdi;
            this.prodi_kode = kodePr;
        },

        setDeleteMK(
            namaProdi,
            kodeMkDelete,
            forceDelete
        ) {
            this.rps_delete = namaProdi;
            this.kode_rps_delete = kodeMkDelete;
            this.isForceDelete = forceDelete;
        },

        resetSelect() {
            this.kode_blok = "";
            this.semester = "";
            this.tipe_sks = ""; 
            this.is_wajib = "";
        },
        
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

            this.nama_prodi_search = "";
            this.prodi_id = "";
            this.prodi_kode = "";

            this.rps_delete = "";
            this.kode_rps_delete = "";

            this.nama_prodi_array = [];
            this.prodi_id_array = [];
            this.prodi_kode_array = [];
        }
    });
});