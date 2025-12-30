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
            $table->enum('status',
                ['Aktif', 'Lulus', 'Mengundurkan Diri', 'Cuti',
                'Drop Out', 'Non-Aktif', 'Pindah', 'Hilang',
                'Meninggal Dunia'
                ])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};