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

        kode_wilayah: "",
        label_kelas: "",
        
        password: "",

        hari_pelaksanaan: "",
        jam_mulai: "",
        jam_berakhir: "",

        tanggal_mulai: "",
        tanggal_berakhir: "",

        kapasitas: "",

        jam_mulai: "",
        jam_berakhir: "",

        tanggal_mulai: "",
        tanggal_berakhir: "",

        sks_menit: 0,

        setValueJadwal(
            kode,
            jadwal,
            deskripsi,
            idPr,
            kodePr,
            prodi,
            departemen,
            fakultas,
            idRPS,
            kodeRPS,
            rps,
            sksRPS,
            wajibRPS,
            drafRPS,
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
                id: idPr,
                kode: kodePr,
                slot1: prodi,
                slot2: departemen,
                slot3: fakultas,
            };

            this.rps_id = idRPS;
            this.nama_rps_search = rps;
            this.rps_items = {
                id: idRPS,
                kode: kodeRPS,
                slot1: rps,
                slot2: sksRPS,
                slot3: wajibRPS,
                slot4: drafRPS,
            };
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteJadwal(namaJadwal, kodeJadwalDelete, forceDelete) {
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

            this.kode_wilayah = "";
            this.label_kelas = "";

            this.password = "";

            this.hari_pelaksanaan = "";

            this.jam_mulai = "";
            this.jam_berakhir = "";

            this.tanggal_mulai = "";
            this.tanggal_berakhir = "";

            this.kapasitas = "";

            for (let i = 1; i <= 16; i++) {
                this[`sesi_${i}`] = "";
                this[`base_sesi_${i}`] = "";
            }
        },

        init() {
            // =========================================
            // AUTO JAM BERAKHIR
            // =========================================
            Alpine.effect(() => {
                const value = this.jam_mulai;

                if (!value) {
                    this.jam_berakhir = "";
                    return;
                }

                const [hour, minute] = value.split(":").map(Number);

                let totalMinute = hour * 60 + minute;

                totalMinute += Number(this.sks_menit || 0);

                const endHour = String(
                    Math.floor(totalMinute / 60) % 24,
                ).padStart(2, "0");

                const endMinute = String(totalMinute % 60).padStart(2, "0");

                this.jam_berakhir = `${endHour}:${endMinute}`;
            });

            // =========================================
            // AUTO TANGGAL BERAKHIR (+6 BULAN)
            // =========================================
            Alpine.effect(() => {
                const value = this.tanggal_mulai;

                if (!value) {
                    this.tanggal_berakhir = "";
                    return;
                }

                const [yearPart, weekPart] = value.split("-W");

                const year = parseInt(yearPart);

                const week = parseInt(weekPart);

                const date = new Date(year, 0, 1);

                date.setDate(date.getDate() + (week - 1) * 7);

                date.setMonth(date.getMonth() + 6);

                const target = new Date(date);

                const dayNum = (target.getDay() + 6) % 7;

                target.setDate(target.getDate() - dayNum + 3);

                const firstThursday = new Date(target.getFullYear(), 0, 4);

                const weekNumber =
                    1 +
                    Math.round(
                        ((target - firstThursday) / 86400000 -
                            3 +
                            ((firstThursday.getDay() + 6) % 7)) /
                            7,
                    );

                const finalYear = target.getFullYear();

                this.tanggal_berakhir = `${finalYear}-W${String(
                    weekNumber,
                ).padStart(2, "0")}`;
            });

            // =========================================
            // GENERATE BASE SESI
            // =========================================
            Alpine.effect(() => {
                const weekValue = this.tanggal_mulai;

                const hari = this.hari_pelaksanaan;

                if (!weekValue || !hari) {
                    return;
                }

                const hariMap = {
                    Senin: 1,
                    Selasa: 2,
                    Rabu: 3,
                    Kamis: 4,
                    Jumat: 5,
                    Sabtu: 6,
                    Minggu: 0,
                };

                const targetDay = hariMap[hari];

                const [yearPart, weekPart] = weekValue.split("-W");

                const year = parseInt(yearPart);

                const week = parseInt(weekPart);

                // ISO WEEK START
                const simple = new Date(year, 0, 1 + (week - 1) * 7);

                const dayOfWeek = simple.getDay() || 7;

                const isoWeekStart = new Date(simple);

                if (dayOfWeek <= 4) {
                    isoWeekStart.setDate(simple.getDate() - dayOfWeek + 1);
                } else {
                    isoWeekStart.setDate(simple.getDate() + 8 - dayOfWeek);
                }

                // CARI HARI SESUAI
                const firstDay = new Date(isoWeekStart);

                while (firstDay.getDay() !== targetDay) {
                    firstDay.setDate(firstDay.getDate() + 1);
                }

                // BASE DATE
                const baseDate = new Date(firstDay);

                // GENERATE BASE
                for (let i = 1; i <= 16; i++) {
                    const sesiDate = new Date(baseDate);

                    sesiDate.setDate(sesiDate.getDate() + (i - 1) * 7);

                    const y = sesiDate.getFullYear();

                    const m = String(sesiDate.getMonth() + 1).padStart(2, "0");

                    const d = String(sesiDate.getDate()).padStart(2, "0");

                    const formatted = `${y}-${m}-${d}`;

                    // SIMPAN BASE
                    this[`base_sesi_${i}`] = formatted;

                    // ISI JIKA MASIH KOSONG
                    this[`sesi_${i}`] = formatted;
                }
            });

            // =========================================
            // SHIFT BERDASARKAN DELTA MINGGU
            // =========================================
            for (let i = 1; i <= 15; i++) {
                Alpine.effect(() => {
                    const current = this[`sesi_${i}`];

                    const base = this[`base_sesi_${i}`];

                    if (!current || !base) {
                        return;
                    }

                    const currentDate = new Date(current);

                    const baseDate = new Date(base);

                    const diffDays = Math.floor(
                        (currentDate - baseDate) / (1000 * 60 * 60 * 24),
                    );

                    // HITUNG GAP 7 HARI
                    const shiftWeek = Math.floor(diffDays / 7);

                    // UPDATE SESI BERIKUT
                    for (let j = i + 1; j <= 16; j++) {
                        const nextBase = this[`base_sesi_${j}`];

                        if (!nextBase) {
                            continue;
                        }

                        const nextDate = new Date(nextBase);

                        nextDate.setDate(nextDate.getDate() + shiftWeek * 7);

                        const y = nextDate.getFullYear();

                        const m = String(nextDate.getMonth() + 1).padStart(
                            2,
                            "0",
                        );

                        const d = String(nextDate.getDate()).padStart(2, "0");

                        this[`sesi_${j}`] = `${y}-${m}-${d}`;
                    }
                });
            }
        },
        isWeekend(dateString) {
            const day = new Date(dateString).getDay();

            return day === 0 || day === 6;
        },

        formatHari(dateString) {
            if (!dateString) return "";

            const date = new Date(dateString);

            return date.toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        },
    });

    window.addEventListener("fill-modal-jadwal", (event) => {
        const j = event.detail.jadwal;
        Alpine.store("jadwal").setValueJadwal(
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
            j.rps_rel?.draf_full || j.draf_full,
        );
    });
});
