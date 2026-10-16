<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_posyandu', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke sumber data
            $table->foreignId('posyandu_id')->constrained('posyandu')->onDelete('cascade');
            $table->foreignId('pemeriksaan_balita_id')->nullable()->constrained('pemeriksaan_balita')->onDelete('set null');
            $table->foreignId('pemeriksaan_bumil_id')->nullable()->constrained('pemeriksaan_bumil')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Tipe laporan
            $table->enum('kategori', ['balita', 'bumil']);
            
            // Periode laporan (untuk filter)
            $table->integer('periode_bulan'); // 1-12
            $table->integer('periode_tahun'); // 2026, dst
            
            // Snapshot identitas (disimpan sebagai JSON agar fleksibel)
            $table->json('identitas');       // nama, nik, tanggal_lahir, nama_ibu, dll
            $table->json('data_pemeriksaan'); // bb, tb, lila, hb, ttd, dst
            $table->json('status_gizi');      // status_bb_u, status_tb_u, dll
            $table->json('imunisasi')->nullable(); // imunisasi yang diberikan
            
            // Metadata validasi
            $table->timestamp('tanggal_validasi');
            
            $table->timestamps();
            
            // Index untuk filter cepat
            $table->index(['posyandu_id', 'periode_tahun', 'periode_bulan']);
            $table->index(['kategori', 'periode_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_posyandu');
    }
};