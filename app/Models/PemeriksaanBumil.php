<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanBumil extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_bumil';
    protected $fillable = [
        'ibu_hamil_id', 'minggu_ke', 'tanggal_periksa',
        'bb', 'lila', 'hemoglobin', 'tablet_tambah_darah', 'pmt_pemulihan',
        'tekanan_darah', 'status_gizi', 'status_kek', 'status_anemia', 'status_td',
        'status_review', 'catatan_bidan', 'kader_id',
    ];
    protected $casts = [
        'tanggal_periksa' => 'date',
        'pmt_pemulihan' => 'boolean',
    ];

    public function ibuHamil() { return $this->belongsTo(IbuHamil::class, 'ibu_hamil_id'); }
    public function kader()    { return $this->belongsTo(User::class, 'kader_id'); }
}