<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IbuHamil extends Model
{
    use HasFactory;

    protected $table = 'ibu_hamil';
    protected $fillable = [
        'nama', 'nik', 'umur', 'hamil_ke', 'klmp_dasawisma',
        'tanggal_pendaftaran', 'nama_suami', 'alamat', 'rw', 'rt', 'posyandu_id'
    ];
    protected $casts = ['tanggal_pendaftaran' => 'date'];

    public function posyandu()    { return $this->belongsTo(Posyandu::class, 'posyandu_id'); }
    public function pemeriksaan() { return $this->hasMany(PemeriksaanBumil::class, 'ibu_hamil_id'); }
    public function imunisasis()  { return $this->hasMany(ImunisasiBumil::class, 'ibu_hamil_id'); }

    public function getUsiaKehamilanMingguAttribute()
    {
        if (!$this->tanggal_pendaftaran) return 0;
        $minggu = $this->tanggal_pendaftaran->diffInWeeks(Carbon::now()) + 1;
        return $minggu > 40 ? 40 : $minggu;
    }

    public function getTaksiranPersalinanAttribute()
    {
        if (!$this->tanggal_pendaftaran) return null;
        return $this->tanggal_pendaftaran->copy()->addDays(280);
    }

        // ==========================================
    // HELPER: Format Usia Kehamilan Lengkap
    // Contoh output: "20 minggu 3 hari"
    // ==========================================
    public function getUsiaKehamilanLengkapAttribute(): string
    {
        if (!$this->tanggal_pendaftaran) return 'Belum ada data';

        $now = \Carbon\Carbon::now();
        $diff = $this->tanggal_pendaftaran->diff($now);

        $totalHari = $diff->days;
        $minggu = floor($totalHari / 7);
        $hari   = $totalHari % 7;

        if ($minggu > 40) {
            $minggu = 40;
            $hari   = 0;
        }

        $parts = [];
        if ($minggu > 0) $parts[] = "{$minggu} minggu";
        if ($hari > 0)   $parts[] = "{$hari} hari";

        return implode(' ', $parts) ?: '0 hari';
    }

    /**
     * Format usia ibu (dari tanggal lahir, kalau ada)
     */
    public function getUmurLengkapAttribute(): string
    {
        if (!$this->umur) return '-';
        return "{$this->umur} tahun";
    }
}