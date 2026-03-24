<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table Utama Mata Kuliah
        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkatan_mk', [1, 2, 3, 4])->default(1);
            $table->string('kode_mk')->nullable();
            $table->char('digit_semester', 2);
            $table->char('digit_mk', 2)->nullable();
            $table->string('nama_matkul');
            $table->integer('semester');
            $table->integer('sks_kuliah')->default(1);
            $table->enum('tipe_sks', [1, 2, 3, 4])->default(1);
            $table->boolean('is_wajib')->default(true);
            $table->text('bahan_kajian')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Table Pivot Prodi - Mata Kuliah
        Schema::create('prodi_pivot_mk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');
            $table->foreignId('mk_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi_pivot_mk');
        Schema::dropIfExists('mata_kuliahs');
    }
};