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
        Schema::table('prodi_pivot_mk', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('mk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prodi_pivot_mk', function (Blueprint $table) {
            //
        });
    }
};
