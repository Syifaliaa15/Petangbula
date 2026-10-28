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
        'bb_pra_hamil', 'tinggi_badan', 'bb', 'lila', 'hemoglobin', 
        'tfu', 'kategori_tfu', 'lingkar_perut', 'taksiran_bb_janin', 'kategori_janin',
        'tablet_tambah_darah', 'pmt_pemulihan', 'tekanan_darah', 'keterangan',
        'status_gizi', 'status_kek', 'status_anemia', 'status_td',
        'status_review', 'catatan_bidan', 'kader_id', 'tindakan_bidan',
    ];
    protected $casts = [
        'tanggal_periksa' => 'date',
        'pmt_pemulihan' => 'boolean',
        'tindakan_bidan' => 'array',
    ];

    public function ibuHamil() { return $this->belongsTo(IbuHamil::class, 'ibu_hamil_id'); }
    public function kader()    { return $this->belongsTo(User::class, 'kader_id'); }
}