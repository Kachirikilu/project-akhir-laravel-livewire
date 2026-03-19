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
        Schema::table('fakultas', function (Blueprint $table) {
            $table->string('kode_fk')->nullable()->unique()->after('nama_fakultas');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('kode_jr')->nullable()->unique()->after('nama_jurusan');
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->string('kode_pr')->nullable()->after('nama_prodi');
        });
    }

    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropColumn('kode_fk');
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropColumn('kode_jr');
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->dropColumn('kode_pr');
        });
    }
};
