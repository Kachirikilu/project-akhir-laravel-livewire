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

            $table->enum('status',
                ['Aktif', 'Tugas Belajar', 'Izin Belajar', 'Cuti Sabatika',
                'Resign', 'Pensiun', 'Diberhentikan', 'Alih Tugas',
                'Meninggal Dunia'
                ])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};