<?php

namespace App\Services\C45;

use App\Models\C45Model;
use Illuminate\Support\Facades\Cache;

/**
 * Memuat model AKTIF dari tabel c45_models (dengan cache 24 jam)
 * dan menjalankan prediksi. Ini satu-satunya akses DB saat prediksi.
 */
class C45ModelRepository
{
    /** @return array{id:int, versi:int, tree:array, akurasi_latih:?float}|null */
    public function muat(string $nama, string $target = 'Prioritas_1'): ?array
    {
        $key   = 'c45_model.' . $nama . '.' . $target;
        $cache = Cache::get($key);
        if (is_array($cache)) {
            return $cache;
        }

        $m = C45Model::where('nama', $nama)
            ->where('target', $target)
            ->where('aktif', true)
            ->orderByDesc('versi')
            ->first();

        if ($m === null) {
            return null; // model belum pernah dilatih
        }

        $payload = array(
            'id'            => $m->id,
            'versi'         => $m->versi,
            'tree'          => $m->tree,
            'akurasi_latih' => $m->akurasi_latih,
        );
        Cache::put($key, $payload, now()->addHours(24));
        return $payload;
    }

    /**
     * Prediksi satu kasus. Null = model belum dilatih.
     * @return array{kelas:?string, confidence:?float, jalur:array, sumber:string}|null
     */
    public function prediksi(string $nama, array $fitur, string $target = 'Prioritas_1'): ?array
    {
        $m = $this->muat($nama, $target);
        if ($m === null) {
            return null;
        }
        return (new C45Predictor($m['tree']))->prediksi($fitur);
    }

    public function hapusCache(string $nama, string $target = 'Prioritas_1'): void
    {
        Cache::forget('c45_model.' . $nama . '.' . $target);
    }
}
