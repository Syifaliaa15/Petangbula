<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Cek dulu, jika belum ada baru tambahkan
        if (!Schema::hasColumn('ibu_hamil', 'no_hp')) {
            Schema::table('ibu_hamil', function (Blueprint $table) {
                $table->string('no_hp', 20)->nullable()->after('nama_suami');
            });
        }

        Schema::table('pemeriksaan_bumil', function (Blueprint $table) {
            if (!Schema::hasColumn('pemeriksaan_bumil', 'bb_pra_hamil')) {
                $table->decimal('bb_pra_hamil', 5, 2)->nullable()->after('minggu_ke');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'tinggi_badan')) {
                $table->decimal('tinggi_badan', 5, 2)->nullable()->after('bb_pra_hamil');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'lingkar_perut')) {
                $table->decimal('lingkar_perut', 5, 2)->nullable()->after('tekanan_darah');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'tfu')) {
                $table->decimal('tfu', 5, 2)->nullable()->after('lingkar_perut');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'kategori_tfu')) {
                $table->string('kategori_tfu')->nullable()->after('tfu');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'taksiran_bb_janin')) {
                $table->decimal('taksiran_bb_janin', 5, 2)->nullable()->after('kategori_tfu');
            }
            if (!Schema::hasColumn('pemeriksaan_bumil', 'kategori_janin')) {
                $table->string('kategori_janin')->nullable()->after('taksiran_bb_janin');
            }
        });
    }

    public function down(): void {
        // Untuk rollback (opsional)
    }
};