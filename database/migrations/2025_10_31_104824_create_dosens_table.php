<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->nullable()->unique();
            $table->string('nidn')->nullable()->unique();
            $table->string('nidk')->nullable()->unique();

            $table->foreignId('user_id')
                  ->constrained() 
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->unique(); 
            $table->foreignId('prodi_id')->nullable(); 
            
            $table->string('name');

            $table->enum('status', [
                'Aktif',                  // Hijau (Produktif)
                'Tugas Belajar',          // Kuning (Transisi/Studi)
                'Izin Belajar',           // Kuning (Transisi/Studi)
                'Cuti Sabatika',          // Kuning (Transisi/Riset)
                'Alih Tugas',             // Orange (Perubahan Jabatan)
                'Resign',                 // Orange (Keluar Prosedural)
                'Pensiun',                // Orange (Keluar Prosedural)
                'Diberhentikan',          // Merah (Masalah/Sanksi)
                'Meninggal Dunia'         // Merah (Permanen)
            ])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};