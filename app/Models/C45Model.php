<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model C4.5 yang sudah dilatih (pohon keputusan tersimpan sebagai JSON).
 * Satu kombinasi (nama, target, versi) = satu pohon; hanya versi terbaru
 * yang berstatus aktif dan dipakai untuk prediksi.
 */
class C45Model extends Model
{
    protected $table = 'c45_models';

    protected $guarded = array('id');

    protected $casts = array(
        'tree'         => 'array',
        'metrik'       => 'array',
        'akurasi_latih'=> 'float',
        'jumlah_data'  => 'integer',
        'versi'        => 'integer',
        'aktif'        => 'boolean',
        'dilatih_pada' => 'datetime',
    );
}
