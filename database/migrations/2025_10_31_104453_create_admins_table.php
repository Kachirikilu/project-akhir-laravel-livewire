<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->nullable()->unique();
            $table->string('nitk')->nullable()->unique();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->unique(); 
            
            $table->foreignId('prodi_id')->nullable();
            $table->string('name');

            $table->enum('status', [
                'Aktif',                  // Hijau (Produktif)
                'Tugas Belajar',          // Kuning (Transisi/Sementara)
                'Mutasi',                 // Kuning (Transisi/Sementara)
                'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                'Resign',                 // Orange (Keluar Prosedural)
                'Pensiun',                // Orange (Keluar Prosedural)
                'Diberhentikan',          // Merah (Masalah/Sanksi)
                'Meninggal Dunia'         // Merah (Permanen)
            ])->default('Aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};