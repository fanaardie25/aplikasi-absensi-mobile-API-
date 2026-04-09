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
        Schema::table('agendas', function (Blueprint $table) {
            // Pilihan: 'L' (Laki-laki), 'P' (Perempuan), atau 'ALL' (Semua)
            $table->string('target_gender')->default('ALL')->after('end_absensi');
            // Pilihan: 'Islam', 'Kristen', dll, atau 'ALL' (Semua Agama)
            $table->string('target_religion')->default('ALL')->after('target_gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            //
        });
    }
};
