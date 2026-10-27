<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_bumil', function (Blueprint $table) {
            // Cek dulu agar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('pemeriksaan_bumil', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tekanan_darah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_bumil', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};