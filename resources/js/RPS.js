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

        deskripsi: "",
        
        kode: "",
        digit_akademik: "",

        matkul_id: "",
        nama_matkul_search: "",
        matkul_items: "",

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
                if (item.scpmk) {
                    let rawSubRefs = item.scpmk.flatMap(sub => sub.ref || []);
                    const combinedSubRef = [...this.ref_scpmk, ...rawSubRefs];
                    this.ref_scpmk = Array.from(new Map(combinedSubRef.map(i => [i.id, i])).values());
                }
                if (item.cpl) {
                    const combinedCPL = [...this.cpl_cpmk, ...item.cpl];
                    this.cpl_cpmk = Array.from(new Map(combinedCPL.map(i => [i.id, i])).values());
                }
                if (item.ref) {
                    const combinedRef = [...this.ref_cpmk, ...item.ref];
                    this.ref_cpmk = Array.from(new Map(combinedRef.map(i => [i.id, i])).values());
                }
            });
        },


        setTypeModal(type) {
            this.typeModal = type;
        },

        setIsEdit(isEdit) {
            this.isEdit = isEdit;
        },

        setValueRPS(
            kode,
            kodeBlok,
            deskripsi,
            idMatkul,
            kodeMK,
            namaMatkul,
            tahunAkademik,
            drafText
        ) {
            this.kode = kode;
            this.digit_akademik = kodeBlok;
            this.deskripsi = deskripsi;

            this.matkul_id = idMatkul;
            this.nama_matkul_search = namaMatkul;
            this.matkul_items = {
                "kode": kodeMK,
                "name": namaMatkul
            };

            this.tahun_akademik = tahunAkademik;

            if (tahunAkademik && tahunAkademik.includes('/')) {
                let parts = tahunAkademik.split('/');
                this.tahun_akademik_1 = parts[0];
                this.tahun_akademik_2 = parts[1];
            } else {
                this.tahun_akademik_1 = "";
                this.tahun_akademik_2 = "";
            }

            this.is_draf = drafText;
        },

        setDeleteRPS(
            namaProdi,
            kodeMkDelete,
            forceDelete
        ) {
            this.rps_delete = namaProdi;
            this.kode_rps_delete = kodeMkDelete;
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
            this.deskripsi = "";

            this.kode = "";

            this.matkul_id = "";
            this.nama_matkul_search = "";
            this.matkul_items = "";

            this.digit_akademik = "";
            this.tahun_akademik = "";
            this.tahun_akademik_1 = "";
            this.tahun_akademik_2 = "";

            this.is_draf = "";
        }
    });
});