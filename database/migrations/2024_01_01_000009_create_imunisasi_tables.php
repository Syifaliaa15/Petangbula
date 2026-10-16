<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imunisasi_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->string('jenis_vaksin');
            $table->date('tanggal_vaksinasi');
            $table->timestamps();
            $table->unique(['balita_id', 'jenis_vaksin']);
        });

        Schema::create('imunisasi_bumil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_hamil_id')->constrained('ibu_hamil')->onDelete('cascade');
            $table->string('jenis_tt');
            $table->date('tanggal_vaksinasi');
            $table->timestamps();
            $table->unique(['ibu_hamil_id', 'jenis_tt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imunisasi_bumil');
        Schema::dropIfExists('imunisasi_balita');
    }
};