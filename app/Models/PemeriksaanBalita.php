<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanBalita extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_balita';
    protected $fillable = [
        'balita_id', 'umur_bulan', 'tanggal_periksa',
        'berat_badan', 'tinggi_badan', 'panjang_badan', 'lingkar_kepala',
        'status_gizi', 'status_bb_u', 'status_tb_u', 'status_bb_tb',
        'imunisasi', 'vitamin_a', 'keterangan',
        'kader_id', 'status_review', 'catatan_bidan',
    ];
    protected $casts = [
        'tanggal_periksa' => 'date',
        'vitamin_a' => 'boolean',
        'imunisasi' => 'array',
    ];

    public function balita() { return $this->belongsTo(Balita::class, 'balita_id'); }
    public function kader()  { return $this->belongsTo(User::class, 'kader_id'); }
}