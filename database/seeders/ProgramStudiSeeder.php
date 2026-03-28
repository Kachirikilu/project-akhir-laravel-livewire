<?php

namespace Database\Seeders;

use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            
            // --- DATA STRUKTUR ---
            $data = [
                [
                    'nama' => 'Teknik',
                    'kode' => 'TEK',
                    'jurusan' => [
                        ['nama' => 'Teknik Elektro', 'kode' => 'TKE'],
                        ['nama' => 'Teknik Kimia', 'kode' => 'TKK'],
                        ['nama' => 'Teknik Sipil', 'kode' => 'TKS'],
                        ['nama' => 'Teknik Arsitektur', 'kode' => 'TAA'],
                    ]
                ],
                [
                    'nama' => 'Ilmu Komputer',
                    'kode' => 'FIK',
                    'jurusan' => [
                        ['nama' => 'Sistem Informasi', 'kode' => 'FSI'],
                        ['nama' => 'Teknik Informatika', 'kode' => 'FTI'],
                        ['nama' => 'Ilmu Komputer', 'kode' => 'FMK'],
                    ]
                ],
            ];

            $strataOptions = ['Sarjana', 'Magister', 'Doktor'];

            foreach ($data as $f) {
                // 1. Create Fakultas
                $fakultas = Fakultas::create([
                    'nama_fakultas' => $f['nama'],
                    'kode_fk' => $f['kode'],
                ]);

                foreach ($f['jurusan'] as $j) {
                    // 2. Create Jurusan
                    $jurusan = Jurusan::create([
                        'fakultas_id' => $fakultas->id,
                        'nama_jurusan' => $j['nama'],
                        'kode_jr' => $j['kode'],
                    ]);

                    foreach ($strataOptions as $strata) {
                        // 3. Create Prodi (S1, S2, S3)
                        Prodi::create([
                            'jurusan_id' => $jurusan->id,
                            'nama_prodi' => $j['nama'], // Nama prodi sama dengan nama jurusan sesuai request
                            'nama_strata' => $strata,
                        ]);
                    }
                }
            }
        });
    }
}