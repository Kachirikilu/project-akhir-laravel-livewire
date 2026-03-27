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
        Schema::table('users', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('prodis', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('jurusans', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('fakultas', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('mata_kuliahs', function (Blueprint $table) { $table->softDeletes(); });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('prodis', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('jurusans', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('fakultas', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('mata_kuliahs', function (Blueprint $table) { $table->dropSoftDeletes(); });

    }
};
