<?php

namespace Database\Seeders;

use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\MataKuliah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // --- 1. MATA KULIAH UNIVERSITAS (TINGKATAN 4) ---
            // Digunakan oleh SEMUA prodi yang ada
            $mkUni = [
                ['nama' => 'Pendidikan Agama', 'digit' => '01'],
                ['nama' => 'Kewarganegaraan', 'digit' => '02'],
            ];

            $allProdiIds = Prodi::pluck('id')->toArray();

            foreach ($mkUni as $item) {
                $mk = MataKuliah::create([
                    'tingkatan_mk' => 4,
                    'nama_mk' => $item['nama'],
                    'kode_mk' => null, // Sesuai request
                    'digit_semester' => '10',
                    'digit_mk' => $item['digit'],
                    'semester' => 1,
                    'sks_kuliah' => 2,
                    'tipe_sks' => 1, // Tatap Muka
                    'is_wajib' => true,
                ]);
                $mk->prodis()->attach($allProdiIds);
            }

            // --- 2. MATA KULIAH FAKULTAS (TINGKATAN 3) ---
            
            // Fakultas Teknik (TEK)
            $mkTeknik = [
                ['nama' => 'Matematika Teknik', 'digit' => '11', 'tipe' => 1],
                ['nama' => 'Fisika Teknik', 'digit' => '12', 'tipe' => 2], // Praktikum
            ];
            $prodiTeknikIds = Prodi::whereHas('jr_rel.fk_rel', fn($q) => $q->where('kode_fk', 'TEK'))->pluck('id');
            
            foreach ($mkTeknik as $item) {
                $mk = MataKuliah::create([
                    'tingkatan_mk' => 3,
                    'nama_mk' => $item['nama'],
                    'digit_semester' => '11',
                    'digit_mk' => $item['digit'],
                    'semester' => 1,
                    'sks_kuliah' => 3,
                    'tipe_sks' => $item['tipe'],
                    'is_wajib' => true,
                ]);
                $mk->prodis()->attach($prodiTeknikIds);
            }

            // Fakultas Ilmu Komputer (FIK)
            $mkFasilkom = [
                ['nama' => 'Dasar Pemrograman', 'digit' => '21', 'tipe' => 2], // Praktikum
                ['nama' => 'Logika Informatika', 'digit' => '22', 'tipe' => 1],
            ];
            $prodiFikIds = Prodi::whereHas('jr_rel.fk_rel', fn($q) => $q->where('kode_fk', 'FIK'))->pluck('id');

            foreach ($mkFasilkom as $item) {
                $mk = MataKuliah::create([
                    'tingkatan_mk' => 3,
                    'nama_mk' => $item['nama'],
                    'digit_semester' => '11',
                    'digit_mk' => $item['digit'],
                    'semester' => 1,
                    'sks_kuliah' => 3,
                    'tipe_sks' => $item['tipe'],
                    'is_wajib' => true,
                ]);
                $mk->prodis()->attach($prodiFikIds);
            }

            // --- 3. MATA KULIAH JURUSAN (TINGKATAN 2) ---
            // Contoh: Teknik Elektro
            $jurusanElektro = Jurusan::where('kode_jr', 'TKE')->first();
            if ($jurusanElektro) {
                $mk = MataKuliah::create([
                    'tingkatan_mk' => 2,
                    'nama_mk' => 'Rangkaian Listrik',
                    'digit_semester' => '21',
                    'digit_mk' => '05',
                    'semester' => 2,
                    'tipe_sks' => 1,
                ]);
                $mk->prodis()->attach($jurusanElektro->prodis->pluck('id'));
            }

            // --- 4. MATA KULIAH PRODI (TINGKATAN 1) ---
            // Contoh: S1 Teknik Elektro
            $prodiS1Elektro = Prodi::where('nama_pr', 'Teknik Elektro')->where('strata', 'Sarjana')->first();
            if ($prodiS1Elektro) {
                // Contoh Tipe 3 (Praktek Lapangan) & Tipe 4 (Simulasi)
                MataKuliah::create([
                    'tingkatan_mk' => 1,
                    'nama_mk' => 'Kerja Praktek',
                    'digit_semester' => '60',
                    'digit_mk' => '99',
                    'semester' => 6,
                    'tipe_sks' => 3, // Praktek Lapangan
                ])->prodis()->attach($prodiS1Elektro->id);

                MataKuliah::create([
                    'tingkatan_mk' => 1,
                    'nama_mk' => 'Pemodelan Sistem',
                    'digit_semester' => '50',
                    'digit_mk' => '08',
                    'semester' => 5,
                    'tipe_sks' => 4, // Simulasi
                ])->prodis()->attach($prodiS1Elektro->id);
            }
        });
    }
}