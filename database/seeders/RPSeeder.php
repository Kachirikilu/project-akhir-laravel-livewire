<?php

namespace Database\Seeders;

use App\Models\Akademik\RPS;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\SubCPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Referensi;
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RPSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan Transaction untuk memastikan jika satu gagal, semua dibatalkan
        DB::transaction(function () {
            
            // --- 1. BUAT MASTER CPL ---
            $cpl1 = CPL::create(['kode_cpl' => 'CPL-01', 'deskripsi' => 'Menerapkan pemikiran logis dan kritis dalam pengembangan IPTEK.']);
            $cpl2 = CPL::create(['kode_cpl' => 'CPL-02', 'deskripsi' => 'Menunjukkan kinerja mandiri, bermutu, dan terukur.']);

            // --- 2. AMBIL DATA PENDUKUNG ---
            $mkIds = MataKuliah::take(5)->pluck('id');
            $dosenId = Dosen::first()->id;

            foreach ($mkIds as $index => $mkId) {
                // Aturan: 3 MK Lengkap (Index 0,1,2), 1 MK Draf (Index 3), 1 MK Kosong (Index 4)
                $isDraf = ($index >= 3);
                
                // A. Buat Header RPS
                $rps = RPS::create([
                    'mk_id' => $mkId,
                    'tahun_akademik' => '2025/2026',
                    'is_draf' => $isDraf,
                    'tanggal_revisi' => now(),
                ]);

                // B. Hubungkan Dosen (Pivot Table)
                $rps->dosens()->attach($dosenId, [
                    'peran' => 'Koordinator',
                    'is_ketua' => true
                ]);

                // C. Buat & Hubungkan Referensi
                $ref = Referensi::create([
                    'kode_ref' => 'REF-' . ($index + 1),
                    'judul' => 'Panduan Kurikulum Merdeka Jilid ' . ($index + 1),
                    'penulis' => 'Tim Akademik UNSRI',
                    'tahun' => 2024,
                    'penerbit' => 'UNSRI Press'
                ]);
                $rps->referensis()->attach($ref->id);

                // --- 3. ISI KONTEN (CPMK & SUB-CPMK) ---
                if ($index < 3) {
                    // MK Lengkap: Buat 4 CPMK dengan total 14 Sub-CPMK
                    $this->seedCompleteContent($rps, $cpl1, $cpl2);
                } 
                elseif ($index == 3) {
                    // MK Draf: Buat konten minimalis
                    $this->seedPartialContent($rps, $cpl1);
                }
                // Index 4 dibiarkan kosong
            }
        });
    }

    private function seedCompleteContent($rps, $cpl1, $cpl2)
    {
        // Kita bagi 14 Sub-CPMK ke dalam 4 CPMK (4+4+4+2)
        for ($i = 1; $i <= 4; $i++) {
            $cpmk = CPMK::create([
                'kode_cpmk' => 'CPMK-' . $i,
                'deskripsi' => "Menguasai konsep dasar keilmuan poin ke-$i."
            ]);

            // Relasi ke RPS & CPL
            $rps->cpmks()->attach($cpmk->id);
            $cpmk->cpls()->attach([$cpl1->id, $cpl2->id]);

            $subLimit = ($i == 4) ? 2 : 4;
            for ($j = 1; $j <= $subLimit; $j++) {
                $subCpmk = SubCPMK::create([
                    'kode_scpmk' => "Sub-$i.$j",
                    'deskripsi' => "Mampu menjelaskan detail teknis $i.$j",
                    'materi' => "Materi Kuliah Bab $i Sub-bab $j",
                    'indikator' => "Ketepatan analisis dan hasil praktikum",
                    'bobot' => 7.14, // 100% / 14 = ~7.14
                ]);
                // Relasi ke CPMK
                $cpmk->sub_cpmks()->attach($subCpmk->id);
            }
        }
    }

    private function seedPartialContent($rps, $cpl1)
    {
        $cpmk = CPMK::create([
            'kode_cpmk' => 'CPMK-1',
            'deskripsi' => 'CPMK Tahap Awal (Masih Draf)'
        ]);
        $rps->cpmks()->attach($cpmk->id);
        $cpmk->cpls()->attach($cpl1->id);

        SubCPMK::create([
            'kode_scpmk' => 'Sub-1.1',
            'deskripsi' => 'Pengenalan Mata Kuliah',
            'materi' => 'Kontrak Perkuliahan',
            'indikator' => 'Kehadiran',
            'bobot' => 10.00,
        ])->cpmks()->attach($cpmk->id);
    }
}