<?php

namespace Database\Seeders;

use App\Models\Akademik\{RPS, CPL, CPMK, SubCPMK, MataKuliah, Referensi};
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RPSeeder extends Seeder
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

            // AMBIL OBJEK MODEL (Bukan cuma ID)
            $mks = MataKuliah::take(15)->get(); 
            $dosenId = Dosen::first()->id ?? 1;

            $tahunAkademik = ['2020/2021', '2021/2022', '2022/2023', '2023/2024', '2024/2025', '2025/2026'];

            foreach ($mks as $index => $mk) {
                $waktuPalsu = match(true) {
                    $index < 3 => now()->subYears(8),
                    $index < 6 => now()->subYears(2),
                    $index < 9 => now()->subYears(1),
                    $index < 12 => now()->subMonths(5),
                    default => now(),
                };

                $deskripsiDummy = "Mata kuliah {$mk->nama_matkul} ({$mk->kode_mk}) ini bertujuan untuk memberikan pemahaman komprehensif mengenai prinsip dasar Teknik Elektro. Materi mencakup analisis teoritis dan implementasi praktis guna mendukung CPL Fakultas Teknik Universitas Sriwijaya.";

                $rps = RPS::create([
                    'mk_id' => $mk->id,
                    'deskripsi' => $deskripsiDummy,
                    'tahun_akademik' => $tahunAkademik[$index % count($tahunAkademik)],
                    'is_draf' => ($index % 4 == 0),
                    'tanggal_revisi' => $waktuPalsu,
                    'created_at' => $waktuPalsu,
                    'updated_at' => $waktuPalsu,
                ]);

                $rps->dosens()->attach($dosenId, ['peran' => 'Koordinator', 'is_ketua' => true]);

                // --- 2. REFERENSI ---
                for ($r = 1; $r <= 2; $r++) {
                    $ref = Referensi::create([
                        'kode_ref' => 'REF-' . $mk->id . '-' . $index . $r,
                        'judul' => "Buku Ajar {$mk->nama_matkul} Versi {$r}.0",
                        'penulis' => 'Dosen Teknik UNSRI',
                        'tahun' => rand(2015, 2025),
                        'penerbit' => 'UNSRI Press',
                        'link' => rand(0, 1) ? 'https://gemini.google.com' : 'https://sisfotenika.unsri.ac.id',
                        'created_at' => $waktuPalsu,
                    ]);
                    $rps->referensis()->attach($ref->id);
                }

                // --- 3. ISI KONTEN ---
                // Perhatikan: Saya passing $mk (objek), bukan $mk->id
                if ($index % 2 == 0) {
                    $this->seedCompleteContent($rps, $cpls, $mk, $waktuPalsu);
                } else {
                    $this->seedPartialContent($rps, $cpls[0], $mk, $waktuPalsu);
                }
            }
        });
    }

    private function seedCompleteContent($rps, $cpls, $mk, $waktu)
    {
        $metodeOptions = ['Teori', 'Praktik', 'Tugas', 'UTS', 'UAS'];

        for ($i = 1; $i <= 3; $i++) {
            $cpmk = CPMK::create([
                'kode_cpmk' => 'CPK' . sprintf('%03d%d', $mk->id, $i),
                'deskripsi' => "Mahasiswa mampu menguasai kompetensi MK-{$mk->id} level-{$i}.",
                'created_at' => $waktu,
            ]);

            $rps->cpmks()->attach($cpmk->id);
            $cpmk->cpls()->attach([$cpls[0]->id, $cpls[1]->id]);

            for ($j = 1; $j <= 2; $j++) {
                SubCPMK::create([
                    'kode_scpmk' => 'SUB' . sprintf('%03d%d%d', $mk->id, $i, $j),
                    'deskripsi' => "Mampu mendemonstrasikan sub-kompetensi {$i}.{$j}",
                    'materi' => "Materi Pokok Bahasan {$i}.{$j}",
                    'metodologi' => "Ceramah dan Diskusi",
                    'indikator' => "Ketepatan konsep",
                    'metode' => $metodeOptions[array_rand($metodeOptions)],
                    'bobot' => rand(5, 15),
                    'created_at' => $waktu,
                ])->cpmks()->attach($cpmk->id);
            }
        }
    }

    private function seedPartialContent($rps, $cpl1, $mk, $waktu)
    {
        $cpmk = CPMK::create([
            'kode_cpmk' => 'CPK' . sprintf('%03d', $mk->id) . 'P',
            'deskripsi' => 'CPMK Dasar untuk ' . $mk->nama_matkul,
            'created_at' => $waktu,
        ]);
        
        $rps->cpmks()->attach($cpmk->id);
        $cpmk->cpls()->attach($cpl1->id);

        SubCPMK::create([
            'kode_scpmk' => 'SUB' . sprintf('%03d', $mk->id) . 'P1',
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