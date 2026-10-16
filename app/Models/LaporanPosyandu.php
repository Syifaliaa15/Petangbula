<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPosyandu extends Model
{
    use HasFactory;

    protected $table = 'laporan_posyandu';
    protected $fillable = [
        'posyandu_id', 'pemeriksaan_balita_id', 'pemeriksaan_bumil_id', 'validated_by',
        'kategori', 'periode_bulan', 'periode_tahun',
        'identitas', 'data_pemeriksaan', 'status_gizi', 'imunisasi',
        'tanggal_validasi',
    ];

    protected $casts = [
        'identitas'         => 'array',
        'data_pemeriksaan'  => 'array',
        'status_gizi'       => 'array',
        'imunisasi'         => 'array',
        'tanggal_validasi'  => 'datetime',
    ];

    // ============================================
    // RELASI
    // ============================================
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function pemeriksaanBalita()
    {
        return $this->belongsTo(PemeriksaanBalita::class, 'pemeriksaan_balita_id');
    }

    public function pemeriksaanBumil()
    {
        return $this->belongsTo(PemeriksaanBumil::class, 'pemeriksaan_bumil_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // ============================================
    // HELPER
    // ============================================
    public function getNamaBulanAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulan[$this->periode_bulan] ?? '-';
    }

    public function getPeriodeLabelAttribute(): string
    {
        return $this->nama_bulan . ' ' . $this->periode_tahun;
    }
}