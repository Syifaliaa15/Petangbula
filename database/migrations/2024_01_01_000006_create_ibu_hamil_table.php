<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->integer('umur')->nullable();
            $table->integer('hamil_ke')->nullable();
            $table->string('klmp_dasawisma')->nullable();
            $table->date('tanggal_pendaftaran')->nullable();
            $table->string('nama_suami')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('rt', 5)->nullable();
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibu_hamil');
    }
};