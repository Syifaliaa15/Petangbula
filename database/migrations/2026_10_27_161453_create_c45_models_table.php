<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c45_models', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->index();          // 'balita' | 'bumil'
            $table->string('target');                 // 'Prioritas_1' | 'Prioritas_2' | 'Prioritas_3'
            $table->json('tree');                     
            $table->json('metrik')->nullable();       
            $table->decimal('akurasi_latih', 5, 2)->nullable();
            $table->unsignedInteger('jumlah_data')->default(0);
            $table->unsignedInteger('versi')->default(1);
            $table->boolean('aktif')->default(true);  
            $table->timestamp('dilatih_pada');
            $table->timestamps();

            $table->unique(['nama', 'target', 'versi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c45_models');
    }
};