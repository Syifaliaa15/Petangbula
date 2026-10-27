<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->decimal('lila', 5, 2)->nullable()->after('tinggi_badan');
            $table->boolean('asi_eksklusif')->default(false)->after('lila');
            $table->string('cara_ukur')->default('tidur')->after('asi_eksklusif'); // 'tidur' = PB, 'berdiri' = TB
        });
    }
    public function down(): void {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->dropColumn(['lila', 'asi_eksklusif', 'cara_ukur']);
        });
    }
};