<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImunisasiBumil extends Model
{
    use HasFactory;

    protected $table = 'imunisasi_bumil';
    protected $fillable = ['ibu_hamil_id', 'jenis_tt', 'tanggal_vaksinasi'];
    protected $casts = ['tanggal_vaksinasi' => 'date'];

    public function ibuHamil() { return $this->belongsTo(IbuHamil::class, 'ibu_hamil_id'); }
}