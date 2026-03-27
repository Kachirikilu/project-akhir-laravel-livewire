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
        Schema::create('cpls', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cpl');
            $table->text('deskripsi');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cpmks', function (Blueprint $table) {
            $table->id();
            $table->char('kode_cpmk', 3);
            $table->string('digit_cpmk', 4);
            $table->text('deskripsi');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sub_cpmks', function (Blueprint $table) {
            $table->id();
            $table->char('kode_scpmk', 4);
            $table->string('digit_scpmk', 6);
            $table->text('deskripsi');
            $table->text('materi');
            $table->text('indikator');
            $table->text('deskripsi_tugas')->nullable();
            $table->integer('waktu_tugas')->default(60);
            $table->integer('waktu_mandiri')->default(60);
            $table->decimal('bobot', 5, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('referensis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penulis');
            $table->year('tahun');
            $table->string('penerbit');
            $table->string('link')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_entities');
    }
};
