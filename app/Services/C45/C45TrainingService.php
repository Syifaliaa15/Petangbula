<?php

namespace App\Services\C45;

use App\Models\C45Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Orkestrasi training: muat data latih -> jalankan C4.5 -> hitung akurasi
 * (opsional 10-fold CV) -> simpan sebagai versi baru di tabel c45_models.
 * Dipakai bersama oleh tombol "Latih Model" (controller) dan `c45:train`.
 */
class C45TrainingService
{
    public function latih(string $nama, string $target = 'Prioritas_1', bool $evaluasi = false): C45Model
    {
        $def = C45DataProvider::definisi($nama);
        if (!in_array($target, $def['target'], true)) {
            throw new RuntimeException("Target '{$target}' tidak tersedia untuk model '{$nama}'.");
        }

        $rows = C45DataProvider::muat($nama);
        if (count($rows) < 10) {
            throw new RuntimeException('Data latih terlalu sedikit (minimal 10 baris).');
        }

        // 1) Latih pohon (algoritma C4.5 berjalan DI SINI, sekali saja)
        $trainer = new C45Trainer();
        $model   = $trainer->latih($rows, $def['atribut'], $target);

        // 2) Akurasi latih (re-substitusi)
        $prediktor = new C45Predictor($model);
        $benar = 0;
        foreach ($rows as $r) {
            if ($prediktor->prediksi($r)['kelas'] === $r[$target]) {
                $benar++;
            }
        }
        $akurasiLatih = count($rows) > 0 ? round(100 * $benar / count($rows), 2) : 0.0;

        $metrik = array(
            'akurasi_latih' => $akurasiLatih,
            'jumlah_aturan' => count((new C45Predictor($model))->aturanIFTHEN()),
        );

        // 3) Opsional: 10-fold CV (hanya saat diminta — proses berat boleh berjalan jarang)
        if ($evaluasi) {
            $metrik['cv10'] = (new C45Evaluator($def['atribut'], $target))->crossValidate($rows, 10);
        }

        // 4) Simpan versi baru; versi lama dinonaktifkan (riwayat tetap tersimpan)
        $baru = DB::transaction(function () use ($nama, $target, $model, $metrik, $rows) {
            $versiTerakhir = (int) C45Model::where('nama', $nama)
                ->where('target', $target)
                ->max('versi');

            C45Model::where('nama', $nama)
                ->where('target', $target)
                ->where('aktif', true)
                ->update(array('aktif' => false));

            return C45Model::create(array(
                'nama'          => $nama,
                'target'        => $target,
                'tree'          => $model,
                'metrik'        => $metrik,
                'akurasi_latih' => $metrik['akurasi_latih'],
                'jumlah_data'   => count($rows),
                'versi'         => $versiTerakhir + 1,
                'aktif'         => true,
                'dilatih_pada'  => now(),
            ));
        });

        // 5) Cache prediksi lama tidak berlaku lagi
        Cache::forget('c45_model.' . $nama . '.' . $target);

        return $baru;
    }
}
