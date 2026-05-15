document.addEventListener("alpine:init", () => {
    Alpine.store("jadwal", {
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_jadwal_delete: "",
        kode_jadwal_delete: "",
        
        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        rps_id_show: "",

        kode_jadwal: "",
        kode_jadwal_1: "",
        kode_jadwal_2: "",
        nama_jadwal: "",
        deskripsi: "",
        
        pr_id: "",
        nama_pr_search: "",
        pr_items: "",
        rps_id: "",
        nama_rps_search: "",
        rps_items: "",

        setValueJadwal(kode, jadwal, deskripsi,
            idPr, kodePr, prodi, departemen, fakultas,
            idRPS, kodeRPS, rps, sksRPS, wajibRPS, drafRPS
        ) {
            this.kode_jadwal = kode;
            this.nama_jadwal = jadwal;
            this.deskripsi = deskripsi;

            if (kode) {
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_jadwal_1 = huruf ? huruf[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_jadwal_2 = angka ? angka[0] : "";
            } else {
                this.kode_jadwal_1 = "";
                this.kode_jadwal_2 = "";
            }

            this.pr_id = idPr;
            this.nama_pr_search = prodi;
            this.pr_items = {
                "id": idPr,
                "kode": kodePr,
                "slot1": prodi,
                "slot2": departemen,
                "slot3": fakultas
            };

            this.rps_id = idRPS;
            this.nama_rps_search = rps;
            this.rps_items = {
                "id": idRPS,
                "kode": kodeRPS,
                "slot1": rps,
                "slot2": sksRPS,
                "slot3": wajibRPS,
                "slot4": drafRPS,
            };
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteJadwal(
            namaJadwal,
            kodeJadwalDelete,
            forceDelete
        ) {
            this.nama_jadwal_delete = namaJadwal;
            this.kode_jadwal_delete = kodeJadwalDelete;
            this.isForceDelete = forceDelete;
        },

        resetShow() {
            this.rps_id_show = "";
        },
        
        reset() {
            this.typeModal = "";
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

            this.kode_jadwal = "",
            this.kode_jadwal_1 = "",
            this.kode_jadwal_2 = "",

            this.nama_jadwal = "";
            this.deskripsi = "";

            this.pr_id = "";
            this.nama_pr_search = "";
            this.pr_items = "";

            this.rps_id = "";
            this.nama_rps_search = "";
            this.rps_items = "";

        }
    });

    window.addEventListener('fill-modal-jadwal', (event) => {
        const j = event.detail.jadwal;
        Alpine.store('jadwal').setValueJadwal(
            j.kode_jadwal || j.kode_kelas,
            j.nama_jadwal || j.nama_kelas,
            j.deskripsi || j.deskripsi_kelas,
            j.pr_id,
            j.pr_rel?.kode_pr || j.kode_pr,
            j.pr_rel?.prodi || j.prodi,
            j.pr_rel?.departemen_dp || j.departemen,
            j.pr_rel?.fakultas_fk || j.fakultas,
            j.rps_id,
            j.rps_rel?.kode_rps || j.kode_rps,
            j.rps_rel?.rps || j.rps,
            j.rps_rel?.sks_full || j.sks_full,
            j.wajib_text || j.wajib_text,
            j.rps_rel?.draf_full || j.draf_full
        );
    });
});