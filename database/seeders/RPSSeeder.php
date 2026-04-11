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
            $cpls = [
                CPL::updateOrCreate(['kode_cpl' => 'CPL01'], ['deskripsi' => 'Menerapkan pemikiran logis, kritis, sistematis, dan inovatif...']),
                CPL::updateOrCreate(['kode_cpl' => 'CPL02'], ['deskripsi' => 'Menunjukkan kinerja mandiri, bermutu, dan terukur...']),
                CPL::updateOrCreate(['kode_cpl' => 'CPL03'], ['deskripsi' => 'Menguasai konsep teoritis bidang pengetahuan tertentu secara umum...']),
            ];

            $mks = MataKuliah::take(15)->get();
            $dosenId = Dosen::first()->id ?? 1;
            $tahunAkademik = ['2020/2021', '2021/2022', '2022/2023', '2023/2024', '2024/2025', '2025/2026'];

            foreach ($mks as $index => $mk) {
                $waktuPalsu = match (true) {
                    $index < 3 => now()->subYears(3),
                    $index < 6 => now()->subYears(2),
                    default => now(),
                };

                $rps = RPS::create([
                    'mk_id' => $mk->id,
                    'deskripsi' => "Mata kuliah {$mk->nama_mk} ({$mk->kode_mk}) ini mencakup analisis teoritis dan implementasi praktis Teknik Elektro.",
                    'akademik' => $tahunAkademik[$index % count($tahunAkademik)],
                    'is_draf' => ($index % 4 == 0),
                    'revisi' => $waktuPalsu,
                    'created_at' => $waktuPalsu,
                    'updated_at' => $waktuPalsu,
                ]);

                $rpsCplIds = collect($cpls)->pluck('id')->random(rand(1, count($cpls)));

                // attach dengan sort_order
                foreach ($rpsCplIds as $order => $cplId) {
                    $rps->cpls()->attach($cplId, [
                        'sort_order' => $order
                    ]);
                }

                $rps->dosens()->attach($dosenId, ['peran' => 'Koordinator', 'is_ketua' => true]);

                // --- 2. REFERENSI (Buat objeknya dulu, simpan ID-nya) ---
                $refIds = [];
                for ($r = 1; $r <= 2; $r++) {
                    $ref = Referensi::create([
                        'kode_ref' => 'REF'.$mk->id.$index.$r,
                        'judul' => "Buku Ajar {$mk->nama_mk} Vol. {$r}",
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
            }
        });
    }

    private function seedCompleteContent($rps, $cpls, $mk, $waktu, $refIds)
    {
        $metodeOptions = [
            'Teori', 'Praktik', 'Tugas', 'UTS', 'UAS',
            'Hasil Projek', 'Kerja Praktek', 'Skripsi',
            'Aktivitas Partisipasif', 'Mandiri'
        ];

        for ($i = 1; $i <= 3; $i++) {

            $cpmk = CPMK::create([
                'kode_cpmk' => 'CPMK'.$mk->id.$i,
                'deskripsi' => 'Mahasiswa mampu menguasai kompetensi tingkat '.($i == 1 ? 'Dasar' : ($i == 2 ? 'Menengah' : 'Lanjut'))." pada mata kuliah {$mk->nama_mk}.",
                'created_at' => $waktu,
            ]);

            // 1. RPS ↔ CPMK
            $rps->cpmks()->attach($cpmk->id, [
                'sort_order' => $i - 1
            ]);

            // 2. CPMK ↔ CPL (fix: selalu array & ada sort_order)
            $randomCpls = collect($cpls)
                ->pluck('id')
                ->random(rand(1, 2));

            $randomCpls = collect($randomCpls)->values();

            foreach ($randomCpls as $order => $cplId) {
                $cpmk->cpls()->attach($cplId, [
                    'sort_order' => $order
                ]);
            }

            // 3. CPMK ↔ Referensi (FIX BUG UTAMA 🔥)
            if (!empty($refIds)) {
                $randomRefs = collect($refIds)
                    ->random(rand(1, count($refIds)));

                $randomRefs = collect($randomRefs)->values(); // 🔥 WAJIB

                foreach ($randomRefs as $order => $refId) {
                    $cpmk->refs()->attach($refId, [
                        'sort_order' => $order
                    ]);
                }
            }

            // 4. SUB-CPMK
            for ($j = 1; $j <= 2; $j++) {

                $sub = SubCPMK::create([
                    'kode_scpmk' => 'SUB'.$i.$j,
                    'deskripsi' => 'Mampu menjelaskan dan menerapkan konsep materi bagian '.$i.'.'.$j,
                    'materi' => 'Topik Bahasan ke-'.$i.'.'.$j,
                    'metodologi' => 'Problem Based Learning',
                    'indikator' => 'Ketepatan dan penguasaan materi',
                    'metode' => $metodeOptions[array_rand($metodeOptions)],
                    'bobot' => rand(5, 15),
                    'created_at' => $waktu,
                ]);

                // Sub ↔ CPMK
                $sub->cpmks()->attach($cpmk->id, [
                    'sort_order' => $j - 1
                ]);

                // Sub ↔ Referensi
                if (!empty($refIds)) {
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
        $cpmk = CPMK::create([
            'kode_cpmk' => 'CPMK'.$mk->id,
            'deskripsi' => 'Memahami prinsip dasar dan fondasi utama dari '.$mk->nama_mk,
            'created_at' => $waktu,
        ]);

        $rps->cpmks()->attach($cpmk->id);

        // Hubungkan ke CPL utama
        $cpmk->cpls()->attach($cpl1->id);

        $sub = SubCPMK::create([
            'kode_scpmk' => 'SUB1',
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
