document.addEventListener("alpine:init", () => {
    Alpine.store("cpmk", {

        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        cpmk_delete: "",
        kode_cpmk_delete: "",
        
        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode_cpmk: "",
        kode_cpmk_1: "",
        kode_cpmk_2: "",
        deskripsi: "",

        count_scpmk: 0,
        total_bobot: 0,

        ref_scpmk: [],

        // update(allSubItems) {
        //     if (!allSubItems || allSubItems.length === 0) {
        //         this.ref_scpmk = [];
        //         return;
        //     }

        //     this.ref_scpmk = [];

        //     allSubItems.forEach(item => {
        //         if (item.scpmk) {
        //             let rawSubRefs = item.scpmk.flatMap(sub => sub.ref || []);
        //             const combinedSubRef = [...this.ref_scpmk, ...rawSubRefs];
        //             this.ref_scpmk = Array.from(new Map(combinedSubRef.map(i => [i.id, i])).values());
        //         }
        //     });
        // },

    update(allSubItems) {
    if (!allSubItems || allSubItems.length === 0) {
        this.ref_scpmk = [];
        return;
    }

    // Ambil semua scpmk dari semua item, lalu ambil semua ref dari setiap scpmk tersebut
    let allRefs = allSubItems.flatMap(item => {
        const scpmkList = item.scpmk || [];
        return scpmkList.flatMap(sub => sub.ref || []);
    });

    // Hilangkan duplikat berdasarkan ID buku
    this.ref_scpmk = Array.from(new Map(allRefs.map(r => [r.id, r])).values());
},

        setValueCPMK(kode, deskripsi) {
            this.kode_cpmk = kode;
            this.deskripsi = deskripsi;

            if (kode) {
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_cpmk_1 = huruf ? huruf[0] : '';
                const angka = kode.match(/\d+/g);
                this.kode_cpmk_2 = angka ? angka[0] : '';
            } else {
                this.kode_cpmk_1 = '';
                this.kode_cpmk_2 = '';
            }
        },

        reset() {
            this.typeModal = "",
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.deskripsi = "";
            this.kode_cpmk = "";
            this.kode_cpmk_1 = "";
            this.kode_cpmk_2 = "";
        },

















        digit_akademik: "",

        mk_id: "",
        nama_mk_search: "",
        mk_items: "",

        akademik: "",
        akademik_1: "",
        akademik_2: "",
        is_draf: "",

        setCountSCPMK(val) {
            this.count_scpmk = val;

            if (val < 14 && (this.is_draf === 0 || this.is_draf === 1)) {
                this.is_draf = 1;
            } else if (val < 14 && this.is_draf === "") {
                this.is_draf = "";
            } 
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
            idMK,
            kodeMK,
            namaMK,
            tahunAkademik,
            isDraf,
            countScpmk,
            totalBobot
        ) {
            t_cpmk = kode;
            this.digit_akademik = kodeBlok;
            this.deskripsi = deskripsi;

            this.mk_id = idMK;
            this.nama_mk_search = namaMK;
            this.mk_items = {
                "id": idMK,
                "kode": kodeMK,
                "slot1": namaMK
            };

            this.akademik = tahunAkademik;

            if (tahunAkademik && tahunAkademik.includes('/')) {
                let parts = tahunAkademik.split('/');
                this.akademik_1 = parts[0];
                this.akademik_2 = parts[1];
            } else {
                this.akademik_1 = "";
                this.akademik_2 = "";
            }

            if (countScpmk < 14) {
                this.is_draf = 1;
            } else {
                this.is_draf = isDraf;
            }
            this.count_scpmk = countScpmk;
            this.total_bobot = totalBobot;
        },

        setDeleteRPS(
            namaProdi,
            kodeMkDelete,
            forceDelete
        ) {
            this.cpmk_delete = namaProdi;
            t_cpmk_cpmk_delete = kodeMkDelete;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     t_cpmk_blok = "";
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

            this._cpmk = "";

            this.mk_id = "";
            this.nama_mk_search = "";
            this.mk_items = "";

            this.digit_akademik = "";
            this.akademik = "";
            this.akademik_1 = "";
            this.akademik_2 = "";

            this.is_draf = "";
        }
    });
});