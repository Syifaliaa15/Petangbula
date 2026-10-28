<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->json('tindakan_bidan')->nullable()->after('catatan_bidan');
        });
        Schema::table('pemeriksaan_bumil', function (Blueprint $table) {
            $table->json('tindakan_bidan')->nullable()->after('catatan_bidan');
        });
    }
    public function down(): void {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) { $table->dropColumn('tindakan_bidan'); });
        Schema::table('pemeriksaan_bumil', function (Blueprint $table) { $table->dropColumn('tindakan_bidan'); });
    }
};