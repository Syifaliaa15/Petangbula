<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImunisasiBalita extends Model
{
    use HasFactory;

    protected $table = 'imunisasi_balita';
    protected $fillable = ['balita_id', 'jenis_vaksin', 'tanggal_vaksinasi'];
    protected $casts = ['tanggal_vaksinasi' => 'date'];

    public function balita() { return $this->belongsTo(Balita::class, 'balita_id'); }
}