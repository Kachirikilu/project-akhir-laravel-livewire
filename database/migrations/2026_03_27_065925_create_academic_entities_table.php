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
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->text('deskripsi');
            $table->string('akademik', 9);
            $table->boolean('is_draf')->default(true);
            $table->date('revisi')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cpls', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cpl');
            $table->text('deskripsi');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cpmks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cpmk', 10);
            $table->text('deskripsi');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sub_cpmks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_scpmk', 10);
            $table->text('deskripsi');
            $table->text('materi');
            $table->text('metodologi');
            $table->text('indikator');
            $table->enum('metode', [
                'Teori', 'Praktik', 'Tugas', 'UTS', 'UAS',
                'Hasil Projek', 'Kerja Praktek', 'Skripsi',
                'Aktivitas Partisipasif', 'Mandiri'
            ])->default('Teori');
            $table->text('deskripsi_tugas')->nullable();
            $table->integer('waktu_tugas')->default(60);
            $table->integer('waktu_mandiri')->default(60);
            $table->decimal('bobot', 5, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('referensis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ref', 10);
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun');
            $table->string('link')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps');
        Schema::dropIfExists('cpls');
        Schema::dropIfExists('cpmks');
        Schema::dropIfExists('sub_cpmks');
        Schema::dropIfExists('referensis');
    }
};
