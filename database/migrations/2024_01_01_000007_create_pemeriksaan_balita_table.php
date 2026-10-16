<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->date('tanggal_periksa');
            $table->integer('umur_bulan');
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->decimal('panjang_badan', 5, 2)->nullable();
            $table->decimal('lingkar_kepala', 5, 2)->nullable();
            $table->string('status_gizi')->nullable();
            $table->string('status_bb_u')->nullable();
            $table->string('status_tb_u')->nullable();
            $table->string('status_bb_tb')->nullable();
            $table->text('imunisasi')->nullable();
            $table->boolean('vitamin_a')->default(false);
            $table->text('keterangan')->nullable();
            $table->string('status_review', 20)->default('pending');
            $table->text('catatan_bidan')->nullable();
            $table->foreignId('kader_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_balita');
    }
};