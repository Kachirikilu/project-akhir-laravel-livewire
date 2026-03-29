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
            
            // --- 1. MASTER CPL (Tanpa Strip) ---
            $cpl1 = CPL::create(['kode_cpl' => 'CPL01', 'deskripsi' => 'Menerapkan pemikiran logis dan kritis...']);
            $cpl2 = CPL::create(['kode_cpl' => 'CPL02', 'deskripsi' => 'Menunjukkan kinerja mandiri...']);

            $mkIds = MataKuliah::take(5)->pluck('id');
            $dosenId = Dosen::first()->id;

            foreach ($mkIds as $index => $mkId) {
                $isDraf = ($index >= 3);
                
                $rps = RPS::create([
                    'mk_id' => $mkId,
                    'tahun_akademik' => '2025/2026',
                    'is_draf' => $isDraf,
                    'tanggal_revisi' => now(),
                ]);

                $rps->dosens()->attach($dosenId, ['peran' => 'Koordinator', 'is_ketua' => true]);

                // --- 2. REFERENSI (Tanpa Strip) ---
                $ref = Referensi::create([
                    'kode_ref' => 'REF' . sprintf('%03d', $mkId) . ($index + 1), 
                    'judul' => 'Panduan Kurikulum Merdeka Jilid ' . ($index + 1),
                    'penulis' => 'Tim Akademik UNSRI',
                    'tahun' => 2024,
                    'penerbit' => 'UNSRI Press'
                ]);
                $rps->referensis()->attach($ref->id);

                // --- 3. ISI KONTEN ---
                if ($index < 3) {
                    $this->seedCompleteContent($rps, $cpl1, $cpl2, $mkId);
                } elseif ($index == 3) {
                    $this->seedPartialContent($rps, $cpl1, $mkId);
                }
            }
        });
    }

    private function seedCompleteContent($rps, $cpl1, $cpl2, $mkId)
    {
        for ($i = 1; $i <= 4; $i++) {
            // Format CPK + ID MK (3 digit) + No Urut (1 digit) = CPK0011
            $kodeCPMK = 'CPK' . sprintf('%03d%d', $mkId, $i);

            $cpmk = CPMK::create([
                'kode_cpmk' => $kodeCPMK,
                'deskripsi' => "Menguasai konsep dasar keilmuan MK-$mkId poin ke-$i."
            ]);

            $rps->cpmks()->attach($cpmk->id);
            $cpmk->cpls()->attach([$cpl1->id, $cpl2->id]);

            $subLimit = ($i == 4) ? 2 : 4;
            for ($j = 1; $j <= $subLimit; $j++) {
                // Format SUB + ID MK (3 digit) + Urutan CPMK + Urutan Sub = SUB00111
                $kodeSub = 'SUB' . sprintf('%03d%d%d', $mkId, $i, $j);

                $subCpmk = SubCPMK::create([
                    'kode_scpmk' => $kodeSub,
                    'deskripsi' => "Mampu menjelaskan detail teknis MK-$mkId pada $i.$j",
                    'materi' => "Materi Kuliah Bab $i Sub-bab $j",
                    'indikator' => "Ketepatan analisis",
                    'bobot' => 7.14,
                ]);
                $cpmk->sub_cpmks()->attach($subCpmk->id);
            }
        }
    }

    private function seedPartialContent($rps, $cpl1, $mkId)
    {
        $kodeCPMK = 'CPK' . sprintf('%03d1', $mkId);

        $cpmk = CPMK::create([
            'kode_cpmk' => $kodeCPMK,
            'deskripsi' => 'CPMK Tahap Awal MK-' . $mkId
        ]);
        $rps->cpmks()->attach($cpmk->id);
        $cpmk->cpls()->attach($cpl1->id);

        SubCPMK::create([
            'kode_scpmk' => 'SUB' . sprintf('%03d11', $mkId),
            'deskripsi' => 'Pengenalan Mata Kuliah',
            'materi' => 'Kontrak Perkuliahan',
            'indikator' => 'Kehadiran',
            'bobot' => 10.00,
        ])->cpmks()->attach($cpmk->id);
    }
}