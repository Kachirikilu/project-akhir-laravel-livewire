<?php

namespace Database\Seeders;

use App\Models\Akademik\{RPS, CPL, CPMK, SubCPMK, MataKuliah, Referensi};
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RPSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            
            // --- 1. MASTER CPL ---
            $cpls = [
                CPL::create(['kode_cpl' => 'CPL01', 'deskripsi' => 'Menerapkan pemikiran logis, kritis, sistematis, dan inovatif...']),
                CPL::create(['kode_cpl' => 'CPL02', 'deskripsi' => 'Menunjukkan kinerja mandiri, bermutu, dan terukur...']),
                CPL::create(['kode_cpl' => 'CPL03', 'deskripsi' => 'Menguasai konsep teoritis bidang pengetahuan tertentu secara umum...']),
            ];

            // Ambil lebih banyak MK untuk variasi data
            $mkIds = MataKuliah::take(15)->pluck('id');
            $dosenId = Dosen::first()->id ?? 1;

            // Variasi Tahun Akademik
            $tahunAkademik = ['2020/2021', '2021/2022', '2022/2023', '2023/2024', '2024/2025', '2025/2026'];

            foreach ($mkIds as $index => $mkId) {
                // Manipulasi Waktu: 
                // Index 0-2: Data 8 tahun lalu
                // Index 3-5: Data 2 tahun lalu
                // Index 6-8: Data 1 tahun lalu
                // Index 9-11: Data 5 bulan lalu (Masuk filter < 6 bulan)
                // Sisanya: Data hari ini
                $waktuPalsu = match(true) {
                    $index < 3 => now()->subYears(8),
                    $index < 6 => now()->subYears(2),
                    $index < 9 => now()->subYears(1),
                    $index < 12 => now()->subMonths(5),
                    default => now(),
                };

                $rps = RPS::create([
                    'mk_id' => $mkId,
                    'tahun_akademik' => $tahunAkademik[$index % count($tahunAkademik)],
                    'is_draf' => ($index % 4 == 0), // Variasi draf/bukan
                    'tanggal_revisi' => $waktuPalsu,
                    'created_at' => $waktuPalsu,
                    'updated_at' => $waktuPalsu,
                ]);

                $rps->dosens()->attach($dosenId, ['peran' => 'Koordinator', 'is_ketua' => true]);

                // --- 2. REFERENSI DENGAN VARIASI TAHUN & LINK ---
                for ($r = 1; $r <= 2; $r++) {
                    $ref = Referensi::create([
                        'kode_ref' => 'REF-' . $mkId . '-' . $index . $r,
                        'judul' => "Buku Ajar MK-{$mkId} Versi {$r}.0",
                        'penulis' => 'Dosen Teknik UNSRI',
                        'tahun' => rand(2015, 2025),
                        'penerbit' => 'UNSRI Press',
                        'link' => rand(0, 1) ? 'https://gemini.google.com' : 'https://sisfotenika.unsri.ac.id',
                        'created_at' => $waktuPalsu,
                    ]);
                    $rps->referensis()->attach($ref->id);
                }

                // --- 3. ISI KONTEN (CPMK & Sub-CPMK) ---
                if ($index % 2 == 0) {
                    $this->seedCompleteContent($rps, $cpls, $mkId, $waktuPalsu);
                } else {
                    $this->seedPartialContent($rps, $cpls[0], $mkId, $waktuPalsu);
                }
            }
        });
    }

    private function seedCompleteContent($rps, $cpls, $mkId, $waktu)
    {
        $metodeOptions = [
            'Teori', 'Praktik', 'Tugas', 'UTS', 'UAS', 
            'Hasil Projek', 'Kerja Praktek', 'Skripsi', 
            'Aktivitas Partisipasif', 'Mandiri'
        ];

        for ($i = 1; $i <= 3; $i++) {
            $cpmk = CPMK::create([
                'kode_cpmk' => 'CPK' . sprintf('%03d%d', $mkId, $i),
                'deskripsi' => "Mahasiswa mampu menguasai kompetensi MK-{$mkId} level-{$i}.",
                'created_at' => $waktu,
            ]);

            $rps->cpmks()->attach($cpmk->id);
            $cpmk->cpls()->attach([$cpls[0]->id, $cpls[1]->id]);

            // Hubungkan CPMK ke Referensi (Pivot baru)
            $cpmk->referensis()->attach($rps->referensis()->pluck('referensis.id'), ['sort_order' => $i]);

            for ($j = 1; $j <= 2; $j++) {
                $subCpmk = SubCPMK::create([
                    'kode_scpmk' => 'SUB' . sprintf('%03d%d%d', $mkId, $i, $j),
                    'deskripsi' => "Mampu mendemonstrasikan sub-kompetensi {$i}.{$j}",
                    'materi' => "Materi Pokok Bahasan {$i}.{$j}",
                    'metodologi' => "Ceramah dan Diskusi Kelompok",
                    'indikator' => "Ketepatan penjelasan konsep",
                    'metode' => $metodeOptions[array_rand($metodeOptions)],
                    'deskripsi_tugas' => "Menyusun laporan analisa kasus MK-{$mkId}",
                    'waktu_tugas' => 120,
                    'waktu_mandiri' => 120,
                    'bobot' => rand(5, 15) + 0.5,
                    'created_at' => $waktu,
                ]);
                
                $cpmk->sub_cpmks()->attach($subCpmk->id);
                
                // Hubungkan Sub-CPMK ke Referensi (Pivot baru)
                $subCpmk->referensis()->attach($rps->referensis()->first()->id, ['sort_order' => $j]);
            }
        }
    }

    private function seedPartialContent($rps, $cpl1, $mkId, $waktu)
    {
        $cpmk = CPMK::create([
            'kode_cpmk' => 'CPK' . sprintf('%03d', $mkId) . 'P',
            'deskripsi' => 'CPMK Dasar untuk Mata Kuliah ' . $mkId,
            'created_at' => $waktu,
        ]);
        
        $rps->cpmks()->attach($cpmk->id);
        $cpmk->cpls()->attach($cpl1->id);

        SubCPMK::create([
            'kode_scpmk' => 'SUB' . sprintf('%03d', $mkId) . 'P1',
            'deskripsi' => 'Pengenalan dan Dasar Teori',
            'materi' => 'Pendahuluan Materi',
            'metodologi' => 'Discovery Learning',
            'indikator' => 'Pemahaman awal',
            'metode' => 'Teori',
            'bobot' => 10.00,
            'created_at' => $waktu,
        ])->cpmks()->attach($cpmk->id);
    }
}