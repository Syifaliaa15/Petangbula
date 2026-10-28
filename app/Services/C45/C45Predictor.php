<?php

namespace App\Services\C45;

/**
 * Prediksi dengan MENELUSURI pohon C4.5 yang sudah dilatih (array JSON).
 *
 * Tidak pernah membaca data latih -> kompleksitas O(kedalaman pohon),
 * praktis sub-milidetik. Inilah pemisahan train/predict yang membuat
 * C4.5 tetap dipakai tanpa membebani web.
 */
class C45Predictor
{
    private array $model;

    /** @param array $model struktur yang dikembalikan C45Trainer::latih() (atau kolom tree di tabel c45_models) */
    public function __construct(array $model)
    {
        $this->model = $model;
    }

    /**
     * Contoh $fitur (balita):
     *   array('Tren_BB' => 'Naik', 'Kelompok_Usia' => '>= 24 bulan',
     *         'Status_Gizi' => 'Pendek', 'LILA' => 'Cukup')
     *
     * @return array{kelas:?string, confidence:?float, jalur:array, sumber:string}
     */
    public function prediksi(array $fitur): array
    {
        $jalur = array();
        $node  = $this->model['pohon'];

        while (is_array($node) && isset($node['tipe']) && $node['tipe'] === 'cabang') {
            $a     = $node['atribut'];
            $nilai = isset($fitur[$a]) ? (string) $fitur[$a] : null;

            if ($nilai !== null && isset($node['cabang'][$nilai])) {
                $jalur[] = $a . ' = ' . $nilai;
                $node    = $node['cabang'][$nilai];
            } else {
                // Nilai fitur tidak dikenal (tidak muncul saat training)
                // -> pakai kelas mayoritas node (mekanisme C4.5 standar).
                $jalur[] = $a . ' = ' . ($nilai === null ? '(kosong)' : $nilai) . ' [nilai tak dikenal]';
                return array(
                    'kelas'      => $node['kelas_mayoritas'],
                    'confidence' => null,
                    'jalur'      => $jalur,
                    'sumber'     => 'fallback_mayoritas',
                );
            }
        }

        // Daun murni / daun mayoritas: confidence = proporsi kelas di daun
        $n = (int) $node['jumlah_data'];
        $k = isset($node['distribusi'][$node['kelas']]) ? (int) $node['distribusi'][$node['kelas']] : $n;

        return array(
            'kelas'      => $node['kelas'],
            'confidence' => ($n > 0) ? round($k / $n, 4) : null,
            'jalur'      => $jalur,
            'sumber'     => 'pohon',
        );
    }

    /**
     * Ekstrak aturan IF-THEN dari pohon (untuk Bab 4 dan halaman web).
     * Return: array of {jika: ["Status_Gizi = Gizi Buruk", ...], maka: kelas, jumlah_data}
     */
    public function aturanIFTHEN(): array
    {
        $aturan = array();
        $this->telusuri($this->model['pohon'], array(), $aturan);
        return $aturan;
    }

    private function telusuri(array $node, array $syarat, array &$aturan): void
    {
        if ($node['tipe'] === 'daun') {
            $aturan[] = array(
                'jika'        => $syarat,
                'maka'        => $node['kelas'],
                'jumlah_data' => $node['jumlah_data'],
            );
            return;
        }
        foreach ($node['cabang'] as $nilai => $anak) {
            $baru   = $syarat;
            $baru[] = $node['atribut'] . ' = ' . $nilai;
            $this->telusuri($anak, $baru, $aturan);
        }
    }
}
