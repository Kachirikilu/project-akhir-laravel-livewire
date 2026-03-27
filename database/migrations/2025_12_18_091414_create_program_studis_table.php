<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_fk')->unique();
            $table->string('nama_fakultas');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->nullable() ->constrained('fakultas')->onDelete('set null');
            $table->string('kode_jr')->nullable();
            $table->string('nama_jurusan');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->nullable() ->constrained('jurusans')->onDelete('set null');
            $table->string('kode_pr')->nullable();
            $table->string('nama_prodi');
            $table->enum('nama_strata', ['Sarjana', 'Magister', 'Doktor'])->default('Sarjana');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fakultas');
        Schema::dropIfExists('jurusans');
        Schema::dropIfExists('prodis');
    }
};
