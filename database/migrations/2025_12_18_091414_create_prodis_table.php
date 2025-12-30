<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->nullable() ->constrained('jurusans')->onDelete('set null');
            $table->string('nama_prodi');
            $table->enum('nama_strata', ['Sarjana', 'Magister', 'Doktor'])->default('Sarjana');;
            $table->timestamps();
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
