<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanDarurat extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_darurat';
    protected $fillable = [
        'nama_pasien', 'kategori', 'jenis_kelamin',
        'tanggal', 'keluhan', 'tindakan', 'bidan_id',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function bidan() { return $this->belongsTo(User::class, 'bidan_id'); }
}