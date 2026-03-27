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
            $table->string('kode_fk')->nullable(false)->change();
        });
        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropUnique(['kode_jr']);
        });
    }

    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table) {
            $table->string('kode_fk')->nullable()->change();
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->unique('kode_jr');
        });
    }
};
