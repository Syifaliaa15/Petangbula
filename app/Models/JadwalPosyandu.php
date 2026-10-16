<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPosyandu extends Model
{
    use HasFactory;

    protected $table = 'jadwal_posyandu';
    protected $fillable = [
        'tanggal', 'waktu_mulai', 'waktu_selesai',
        'lokasi', 'kegiatan', 'keterangan',
        'posyandu_id', 'kader_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function kader()
    {
        return $this->belongsTo(User::class, 'kader_id');
    }

    // Helper: apakah jadwal masih akan datang
    public function getIsUpcomingAttribute(): bool
    {
        return $this->tanggal->isFuture() || $this->tanggal->isToday();
    }
}