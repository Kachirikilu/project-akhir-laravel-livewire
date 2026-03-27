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
        // Dosen Pengajar (RPS - User)
        Schema::create('rps_pivot_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->enum('peran', [
                'Koordinator',
                'Pengajar',
                'Asisten'
            ])->default('Pengajar');
            $table->boolean('is_ketua')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Prodis - CPL (Ownership)
        Schema::create('prodi_pivot_cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');
            $table->foreignId('cpl_id')->constrained('cpls')->onDelete('cascade');
        });

        // CPMK - CPL
        Schema::create('cpmk_pivot_cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpl_id')->constrained('cpls')->onDelete('cascade');
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
        });

        // RPS - CPMK
        Schema::create('rps_pivot_cpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // CPMK - Sub-CPMK
        Schema::create('cpmk_pivot_scpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmk_id')->constrained('cpmks')->onDelete('cascade');
            $table->foreignId('scpmk_id')->constrained('sub_cpmks')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
        });

        // RPS - Referensi
        Schema::create('rps_pivot_referensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rps_id')->constrained('rps')->onDelete('cascade');
            $table->foreignId('referensi_id')->constrained('referensis')->onDelete('cascade');
            $table->string('kategori')->default('utama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_pivots');
    }
};
