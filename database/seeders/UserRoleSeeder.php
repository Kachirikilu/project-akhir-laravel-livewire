<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\ProgramStudi\Prodi; // Import model Prodi
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPw = Hash::make('12345678');

        DB::transaction(function () use ($defaultPw) {
            
            // Ambil ID Prodi Utama (misal: Teknik Elektro)
            $prodiUtama = Prodi::first();
            
            // Pastikan ada prodi, jika tidak ada, beri peringatan atau buat dummy
            if (!$prodiUtama) {
                throw new \Exception("Tabel prodis kosong! Jalankan SilsilahSeeder terlebih dahulu.");
            }

            // --- 1. DATA SPESIFIK (WILDAN) ---

            // Admin Wildan
            $adminUser = User::create([
                'email' => 'muttaqien.wildan12@gmail.com',
                'password' => $defaultPw,
            ]);
            Admin::create([
                'user_id' => $adminUser->id,
                'pr_id' => $prodiUtama->id, // Tambahkan pr_id
                'nip' => '199001012024011001',
                'name' => 'Wildan Athif Muttaqien (Admin)',
                'status' => 'Aktif',
            ]);

            // Dosen Wildan
            $dosenUser = User::create([
                'email' => 'muttaqien.wildan13@gmail.com',
                'password' => $defaultPw,
            ]);
            Dosen::create([
                'user_id' => $dosenUser->id,
                'pr_id' => $prodiUtama->id, // Tambahkan pr_id
                'nip' => '199001012024011002',
                'nidn' => '0012345678',
                'name' => 'Wildan Athif Muttaqien (Dosen)',
                'status' => 'Aktif',
            ]);

            // Mahasiswa Wildan
            $mhsUser = User::create([
                'email' => 'muttaqien.wildan14@gmail.com',
                'password' => $defaultPw,
            ]);
            Mahasiswa::create([
                'user_id' => $mhsUser->id,
                'pr_id' => $prodiUtama->id, // Tambahkan pr_id
                'nim' => '06111281722000',
                'name' => 'Wildan Athif Muttaqien (Mhs)',
                'tahun_angkatan' => 2021,
                'status' => 'Aktif',
            ]);

            // --- 2. DATA DUMMY TAMBAHAN (LOOPING) ---
            
            // Ambil semua ID prodi yang tersedia untuk variasi data dummy
            $prodiIds = Prodi::pluck('id')->toArray();

            for ($i = 1; $i <= 2; $i++) {
                // Gunakan prodi acak dari koleksi prodi yang ada
                $randomProdiId = $prodiIds[array_rand($prodiIds)];

                // Admin tambahan
                $uA = User::create(['email' => "admin.test{$i}@gmail.com", 'password' => $defaultPw]);
                Admin::create([
                    'user_id' => $uA->id, 
                    'pr_id' => $randomProdiId,
                    'name' => "Admin Test {$i}", 
                    'nip' => "888{$i}", 
                    'status' => 'Aktif'
                ]);

                // Dosen tambahan
                $uD = User::create(['email' => "dosen.test{$i}@gmail.com", 'password' => $defaultPw]);
                Dosen::create([
                    'user_id' => $uD->id, 
                    'pr_id' => $randomProdiId,
                    'name' => "Dosen Test {$i}", 
                    'nip' => "777{$i}", 
                    'status' => 'Aktif'
                ]);

                // Mahasiswa tambahan
                $uM = User::create(['email' => "mhs.test{$i}@gmail.com", 'password' => $defaultPw]);
                Mahasiswa::create([
                    'user_id' => $uM->id, 
                    'pr_id' => $randomProdiId,
                    'name' => "Mhs Test {$i}", 
                    'nim' => "0611{$i}", 
                    'tahun_angkatan' => 2021, 
                    'status' => 'Aktif'
                ]);
            }
        });
    }
}