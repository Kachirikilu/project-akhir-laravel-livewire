<?php

namespace Database\Seeders;

use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RPSSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // --- 1. MASTER CPL ---
            $cpls = [];
            for ($i = 1; $i <= 24; $i++) {
                $kodeCpl = sprintf('CPL%02d', $i);
                $deskripsi = "Capaian pembelajaran lulusan ke-$i: mahasiswa mampu mengaplikasikan kompetensi akademik dan profesional secara efektif.";

                $cpls[] = CPL::updateOrCreate([
                    'kode_cpl' => $kodeCpl,
                ], [
                    'deskripsi' => $deskripsi,
                ]);
            }

            $mks = MataKuliah::take(9)->get();
            $dosenId = Dosen::first()->id ?? 1;
            $tahunAkademik = ['2020/2021', '2021/2022', '2022/2023', '2023/2024', '2024/2025', '2025/2026'];
            $rpsCount = 0;

            foreach ($mks as $index => $mk) {
                for ($copy = 0; $copy < 2; $copy++) {
                    if ($rpsCount >= 15) {
                        break 2;
                    }

                    $waktuPalsu = match (true) {
                        $index < 3 => now()->subYears(3),
                        $index < 6 => now()->subYears(2),
                        default => now(),
                    };

                    $akademikIndex = ($index * 2 + $copy) % count($tahunAkademik);
                    $akademik = $tahunAkademik[$akademikIndex];

                    $rps = RPS::create([
                        'mk_id' => $mk->id,
                        'deskripsi' => 'Mata kuliah '.$mk->nama_mk.' ('.$mk->kode_mk.') - RPS ke '.($copy + 1).' menjelaskan analisis teoritis dan implementasi praktis Teknik Elektro.',
                        'akademik' => $akademik,
                        'is_draf' => ($rpsCount % 4 == 0),
                        'revisi' => $waktuPalsu,
                        'created_at' => $waktuPalsu,
                        'updated_at' => $waktuPalsu,
                    ]);

                    $rpsCplIds = collect($cpls)->pluck('id')->random(rand(1, count($cpls)));

                    // attach dengan sort_order
                    foreach ($rpsCplIds as $order => $cplId) {
                        $rps->cpls()->attach($cplId, [
                            'sort_order' => $order,
                        ]);
                    }

                    $rps->dosens()->attach($dosenId, ['peran' => 'Koordinator', 'is_ketua' => true]);

                    // --- 2. REFERENSI (Buat objeknya dulu, simpan ID-nya) ---
                    $refIds = [];
                    for ($r = 1; $r <= 2; $r++) {
                        $ref = Referensi::create([
                            'kode_ref' => 'REF'.$mk->id.$rpsCount.$r,
                            'judul' => "Buku Ajar {$mk->nama_mk} Vol. {$r} (RPS {$rpsCount})",
                            'penulis' => 'Dosen Teknik UNSRI',
                            'tahun' => rand(2020, 2026),
                            'penerbit' => 'UNSRI Press',
                            'link' => 'https://sisfotenika.unsri.ac.id',
                            'created_at' => $waktuPalsu,
                        ]);

                        // Opsi A: Jika tetap ingin tampil di daftar pustaka RPS (Sangat Disarankan)
                        $rps->refs()->attach($ref->id);

                        $refIds[] = $ref->id;
                    }

                    // --- 3. ISI KONTEN ---
                    // Teruskan $refIds ke fungsi helper
                    if ($index % 2 == 0) {
                        $this->seedCompleteContent($rps, $cpls, $mk, $waktuPalsu, $refIds);
                    } else {
                        $this->seedPartialContent($rps, $cpls[0], $mk, $waktuPalsu, $refIds);
                    }

                    $rpsCount++;
                }
            }
        });
    }

    private function seedCompleteContent($rps, $cpls, $mk, $waktu, $refIds)
    {
        $metodeOptions =
        [
            // --- Evaluasi OBE/Projek (Tatap Muka/Tugas) ---
            'Teori',
            'Aktivitas Partisipasif',
            'Tugas',
            'Mandiri',

            // --- Evaluasi Formal (Umum) ---
            'UTS', 'UAS', 'Kuis',
            'Laporan Akhir',
            'Hasil Projek',

            // --- Evaluasi Berbasis Kinerja (Praktikum/Lapangan/Simulasi) ---
            'Skripsi',
            'Kerja Praktek',
            'Responsi',
            'Logbook',
            'Portofolio',
        ];

        for ($i = 1; $i <= 3; $i++) {

            $kodeCpmk = "CPMK{$mk->id}{$i}";
            $cpmk = CPMK::updateOrCreate([
                'kode_cpmk' => $kodeCpmk,
            ], [
                'deskripsi' => 'Mahasiswa mampu menguasai kompetensi tingkat '.($i == 1 ? 'Dasar' : ($i == 2 ? 'Menengah' : 'Lanjut'))." pada mata kuliah {$mk->nama_mk}.",
                'created_at' => $waktu,
            ]);

            // 1. RPS ↔ CPMK
            $rps->cpmks()->attach($cpmk->id, [
                'sort_order' => $i - 1,
            ]);

            // 2. CPMK ↔ CPL (fix: selalu array & ada sort_order)
            $randomCpls = collect($cpls)
                ->pluck('id')
                ->random(rand(1, 2));

            $randomCpls = collect($randomCpls)->values();

            foreach ($randomCpls as $order => $cplId) {
                $cpmk->cpls()->attach($cplId, [
                    'sort_order' => $order,
                ]);
            }

            // 3. CPMK ↔ Referensi (FIX BUG UTAMA 🔥)
            if (! empty($refIds)) {
                $randomRefs = collect($refIds)
                    ->random(rand(1, count($refIds)));

                $randomRefs = collect($randomRefs)->values(); // 🔥 WAJIB

                foreach ($randomRefs as $order => $refId) {
                    $cpmk->refs()->attach($refId, [
                        'sort_order' => $order,
                    ]);
                }
            }

            // 4. SUB-CPMK
            for ($j = 1; $j <= 2; $j++) {

                $kodeScpmk = "SUBMK{$mk->id}{$i}{$j}";
                $sub = SubCPMK::updateOrCreate([
                    'kode_scpmk' => $kodeScpmk,
                ], [
                    'deskripsi' => 'Mampu menjelaskan dan menerapkan konsep materi bagian '.$i.'.'.$j,
                    'materi' => 'Topik Bahasan ke-'.$i.'.'.$j,
                    'metodologi' => 'Problem Based Learning',
                    'indikator' => 'Ketepatan dan penguasaan materi',
                    'metode' => $metodeOptions[array_rand($metodeOptions)],
                    'bobot' => rand(2, 15),
                    'created_at' => $waktu,
                ]);

                // Sub ↔ CPMK
                $sub->cpmks()->attach($cpmk->id, [
                    'sort_order' => $j - 1,
                ]);

                // Sub ↔ Referensi
                if (! empty($refIds)) {
                    $sub->refs()->attach(
                        $refIds[array_rand($refIds)],
                        ['sort_order' => 0]
                    );
                }
            }
        }
    }

    private function seedPartialContent($rps, $cpl1, $mk, $waktu, $refIds)
    {
        // Versi simpel: 1 CPMK, 1 CPL, 1 Sub-CPMK
        $kodeCpmk = 'CPMK'.$mk->id;
        $cpmk = CPMK::updateOrCreate([
            'kode_cpmk' => $kodeCpmk,
        ], [
            'deskripsi' => 'Memahami prinsip dasar dan fondasi utama dari '.$mk->nama_mk,
            'created_at' => $waktu,
        ]);

        $rps->cpmks()->attach($cpmk->id);

        // Hubungkan ke CPL utama
        $cpmk->cpls()->attach($cpl1->id);

        $kodeScpmk = 'SUB1'.$mk->id;
        $sub = SubCPMK::updateOrCreate([
            'kode_scpmk' => $kodeScpmk,
        ], [
            'deskripsi' => 'Mendeskripsikan ruang lingkup mata kuliah secara umum',
            'materi' => 'Pendahuluan dan Kontrak Perkuliahan',
            'metodologi' => 'Discovery Learning',
            'indikator' => 'Keaktifan mahasiswa',
            'metode' => 'Teori',
            'bobot' => 10.00,
            'created_at' => $waktu,
        ]);

        $sub->cpmks()->attach($cpmk->id);

        if (! empty($refIds)) {
            $sub->refs()->attach($refIds[0]);
        }
    }
}
