<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            
            $table->string('nim')->unique(); 

            $table->foreignId('user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->unique(); 

            $table->foreignId('prodi_id')->nullable(); 
            
            $table->string('name');
            $table->year('tahun_angkatan');
            $table->date('tanggal_yudisium')->nullable(); 
            $table->date('tanggal_wisuda')->nullable();
            $table->enum('status', [
                'Aktif',                  // Hijau (Aktif Kuliah)
                'Lulus',                  // Biru (Output Positif)
                'Cuti',                   // Kuning (Jeda Resmi)
                'Pindah',                 // Kuning (Transisi Keluar)
                'Non-Aktif',              // Orange (Masalah Administrasi)
                'Mengundurkan Diri',      // Orange (Keluar Prosedural)
                'Drop Out',               // Merah (Masalah Akademik/Sanksi)
                'Hilang',                 // Merah (Tanpa Kabar/Ghaib)
                'Meninggal Dunia'         // Merah (Permanen)
            ])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};