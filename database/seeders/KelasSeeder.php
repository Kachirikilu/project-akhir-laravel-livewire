<?php

namespace Database\Seeders;

use App\Models\Akademik\RPS;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\SesiKelas;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil RPS yang memiliki 14-16 SCPMK melalui CPMK
        $allRps = RPS::with(['cpmks.scpmks', 'mk_rel.prodis'])->get();

        foreach ($allRps as $rps) {
            // Ambil SCPMK unik dari semua CPMK yang terhubung
            $scpmkList = $rps->cpmks->flatMap(function ($cpmk) {
                return $cpmk->scpmks;
            })->unique('id')->sortBy('pivot.sort_order')->values();

            $totalScpmk = $scpmkList->count();

            // Filter sesuai kriteria Anda (14-16)
            if ($totalScpmk < 14 || $totalScpmk > 16) {
                continue;
            }

            $prodis = $rps->mk_rel->prodis;

            foreach ($prodis as $prodi) {
                DB::transaction(function () use ($rps, $scpmkList, $totalScpmk, $prodi) {
                    $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][rand(0, 4)];
                    $tglMulai = now()->addDays(rand(1, 30));

                    // 2. Buat Kelas sesuai Schema baru
                    $kelas = Kelas::create([
                        'kode_kelas' => strtoupper(str()->random(3)) . '-' . rand(100, 999),
                        'rps_id' => $rps->id,
                        'pr_id' => $prodi->id,
                        'nama_kelas' => 'Kelas ' . $rps->deskripsi . ' - ' . $prodi->nama_pr,
                    ]);

                    // Tambahkan KelasJadwal (Min 2 jadwal)
                    $wilayah = ['IDL', 'PLG'][rand(0, 1)];
                    $labels = ['A', 'B', 'C', 'D'];
                    $jumlahJadwal = rand(2, 4);

                    for ($i = 0; $i < $jumlahJadwal; $i++) {
                        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][rand(0, 4)];
                        $tglMulai = now()->addDays(rand(1, 30));

                        $jadwal = $kelas->jadwals()->create([
                            'kode_wilayah' => $wilayah,
                            'label_kelas' => $labels[$i],
                            'tanggal_mulai' => $tglMulai,
                            'tanggal_berakhir' => (clone $tglMulai)->addMonths(4),
                            'hari_pelaksanaan' => $hari,
                            'jam_mulai' => '08:00:00',
                            'jam_berakhir' => '10:30:00',
                            'kapasitas' => rand(30, 40),
                        ]);

                        // 3. Pasang Mahasiswa (max kapasitas)
                        $mhsIds = Mahasiswa::inRandomOrder()->take(rand(20, $jadwal->kapasitas))->pluck('id');
                        $jadwal->mahasiswas()->attach($mhsIds);

                        $scpmkIndex = 0;

                        // 4. Generate 16 Sesi
                        for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {
                            $sesi = SesiKelas::create([
                                'kj_id' => $jadwal->id,
                                'pertemuan_ke' => $pertemuan,
                                // Tanggal sesi otomatis bertambah 1 minggu tiap pertemuan
                                'tanggal' => (clone $tglMulai)->addWeeks($pertemuan - 1),
                                'password' => strtoupper(str()->random(6)),
                                'catatan' => "Sesi rutin pertemuan ke-$pertemuan",
                            ]);

                            // Logika UTS di Sesi 8
                            if ($pertemuan == 8 && $totalScpmk < 16 && $rps->bobot_uts) {
                                $sesi->override()->create([
                                    'deskripsi' => 'Ujian Tengah Semester',
                                    'metode' => 'UTS',
                                    'bobot' => $rps->bobot_uts,
                                ]);
                                continue; 
                            }

                            // Logika UAS di Sesi 16
                            if ($pertemuan == 16 && $totalScpmk < 16 && $rps->bobot_uas) {
                                $sesi->override()->create([
                                    'deskripsi' => 'Ujian Akhir Semester',
                                    'metode' => 'UAS',
                                    'bobot' => $rps->bobot_uas,
                                ]);
                                continue;
                            }

                            // Mapping SCPMK ke Sesi
                            if (isset($scpmkList[$scpmkIndex])) {
                                $scpmkIndex++;
                            }
                        }
                    }
                });
            }
        }
    }
}