<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_bumil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_hamil_id')->constrained('ibu_hamil')->onDelete('cascade');
            $table->integer('minggu_ke');
            $table->date('tanggal_periksa');
            $table->decimal('bb', 5, 2)->nullable();
            $table->decimal('lila', 5, 2)->nullable();
            $table->decimal('hemoglobin', 4, 1)->nullable();
            $table->integer('tablet_tambah_darah')->default(0);
            $table->boolean('pmt_pemulihan')->default(false);
            $table->string('tekanan_darah')->nullable();
            $table->string('status_gizi')->nullable();
            $table->string('status_kek')->nullable();
            $table->string('status_anemia')->nullable();
            $table->string('status_td')->nullable();
            $table->string('status_review')->default('pending');
            $table->text('catatan_bidan')->nullable();
            $table->foreignId('kader_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_bumil');
    }
};