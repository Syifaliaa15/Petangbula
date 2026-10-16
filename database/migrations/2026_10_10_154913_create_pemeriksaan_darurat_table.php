<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_darurat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pasien');
            $table->enum('kategori', ['balita', 'bumil']);
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal');
            $table->text('keluhan');
            $table->text('tindakan')->nullable();
            $table->foreignId('bidan_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_darurat');
    }
};