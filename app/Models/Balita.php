<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Balita extends Model
{
    use HasFactory;

    protected $table = 'balita';
    protected $fillable = [
        'nama', 'nik', 'tanggal_lahir', 'jenis_kelamin',
        'nama_ibu', 'nama_ayah', 'alamat', 'rw', 'rt', 'posyandu_id'
    ];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function pemeriksaan() { return $this->hasMany(PemeriksaanBalita::class, 'balita_id'); }
    public function imunisasis()  { return $this->hasMany(ImunisasiBalita::class, 'balita_id'); }
    public function posyandu()    { return $this->belongsTo(Posyandu::class, 'posyandu_id'); }

    public function getUmurBulanSekarangAttribute()
    {
        $umur = $this->tanggal_lahir->diffInMonths(Carbon::now());
        return $umur > 59 ? 59 : $umur;
    }

    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir->format('d M Y');
    }

        // ==========================================
    // HELPER: Format Usia Lengkap
    // Contoh output: "2 tahun 5 bulan 1 hari"
    // ==========================================

    /**
     * Umur lengkap dengan format tahun bulan hari
     */
    public function getUmurLengkapAttribute(): string
    {
        if (!$this->tanggal_lahir) return '-';

        $now = \Carbon\Carbon::now();
        $diff = $this->tanggal_lahir->diff($now);

        $tahun = $diff->y;
        $bulan = $diff->m;
        $hari  = $diff->d;

        return $this->formatUsia($tahun, $bulan, $hari);
    }

    /**
     * Umur lengkap sampai tanggal tertentu (untuk buku KIA per-bulan)
     * @param Carbon $tanggalReferensi
     */
    public function getUmurLengkapSampaiAttribute(): string
    {
        return $this->umur_lengkap; // alias
    }

    /**
     * Helper: Format usia dalam bahasa Indonesia
     */
    private function formatUsia(int $tahun, int $bulan, int $hari): string
    {
        $parts = [];
        if ($tahun > 0) $parts[] = "{$tahun} tahun";
        if ($bulan > 0) $parts[] = "{$bulan} bulan";
        if ($hari > 0 || empty($parts)) $parts[] = "{$hari} hari";

        return implode(' ', $parts);
    }

    /**
     * Umur lengkap sampai tanggal tertentu (custom)
     */
    public function umurLengkapSampai($tanggal): string
    {
        if (!$this->tanggal_lahir) return '-';

        $diff = $this->tanggal_lahir->diff($tanggal);
        return $this->formatUsia($diff->y, $diff->m, $diff->d);
    }
}