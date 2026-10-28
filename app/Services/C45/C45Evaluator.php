<?php

namespace App\Services\C45;

/**
 * Evaluasi model C4.5:
 *  - akurasi & confusion matrix pada satu himpunan data
 *  - presisi / recall / F1 per kelas (+ rata-rata makro)
 *  - validasi silang k-fold TERSTRATIFIKASI (deterministik via mt_srand)
 *
 * Evaluasi hanya dijalankan saat tombol "Evaluasi" / training dengan opsi
 * --evaluate, bukan saat prediksi.
 */
class C45Evaluator
{
    private array  $atribut;
    private string $target;

    public function __construct(array $atribut, string $target)
    {
        $this->atribut = array_values($atribut);
        $this->target  = $target;
    }

    /** Evaluasi satu model pada himpunan data (mis. akurasi latih / uji). */
    public function evaluasi(array $rows, array $model): array
    {
        $prediktor = new C45Predictor($model);
        $kelas     = $this->kelasGabungan($rows, $model);

        $matriks = array();
        foreach ($kelas as $a) {
            foreach ($kelas as $p) {
                $matriks[$a][$p] = 0;
            }
        }

        $benar = 0;
        foreach ($rows as $r) {
            $p = $prediktor->prediksi($r)['kelas'];
            if (isset($matriks[$r[$this->target]][$p])) {
                $matriks[$r[$this->target]][$p]++;
            }
            if ($p === $r[$this->target]) {
                $benar++;
            }
        }

        $total = count($rows);
        return array(
            'jumlah_data'     => $total,
            'benar'           => $benar,
            'akurasi'         => $total > 0 ? round(100 * $benar / $total, 2) : 0.0,
            'confusion_matrix'=> $matriks,
            'metrik_per_kelas'=> $this->metrikPerKelas($matriks),
        );
    }

    /**
     * Validasi silang k-fold terstratifikasi.
     * Setiap lipatan dilatih ulang dengan data (k-1) lipatan -> mengukur
     * kemampuan GENERALISASI, bukan sekadar menghafal data latih.
     */
    public function crossValidate(array $rows, int $k = 10, int $seed = 42): array
    {
        $n = count($rows);
        if ($k < 2 || $n < $k) {
            return array('k' => $k, 'akurasi_rata2' => null, 'catatan' => 'Data terlalu sedikit untuk CV.');
        }

        // 1) Kelompokkan indeks per kelas, acak deterministik per kelas
        $perKelas = array();
        foreach ($rows as $i => $r) {
            $perKelas[$r[$this->target]][] = $i;
        }
        mt_srand($seed);
        foreach ($perKelas as $kl => $indeks) {
            $perKelas[$kl] = $this->acak($indeks);
        }

        // 2) Bagi round-robin menjadi k lipatan (terstratifikasi)
        $lipatan = array_fill(0, $k, array());
        $j = 0;
        foreach ($perKelas as $indeks) {
            foreach ($indeks as $i) {
                $lipatan[$j % $k][] = $i;
                $j++;
            }
        }

        // 3) Latih pada k-1 lipatan, uji pada 1 lipatan; gabungkan matriks
        $kelas  = $this->kolomUnikTerurut($rows);
        $gabung = array();
        foreach ($kelas as $a) {
            foreach ($kelas as $p) {
                $gabung[$a][$p] = 0;
            }
        }

        $totalBenar = 0;
        $totalUji   = 0;
        $detail     = array();

        foreach ($lipatan as $ke => $indeksUji) {
            if (count($indeksUji) === 0) {
                continue;
            }
            $ujiSet = array_flip($indeksUji);
            $latih  = array();
            foreach ($rows as $i => $r) {
                if (!isset($ujiSet[$i])) {
                    $latih[] = $r;
                }
            }

            $model     = (new C45Trainer())->latih($latih, $this->atribut, $this->target);
            $prediktor = new C45Predictor($model);

            $benar = 0;
            foreach ($indeksUji as $i) {
                $p = $prediktor->prediksi($rows[$i])['kelas'];
                if (isset($gabung[$rows[$i][$this->target]][$p])) {
                    $gabung[$rows[$i][$this->target]][$p]++;
                }
                if ($p === $rows[$i][$this->target]) {
                    $benar++;
                }
            }

            $totalBenar += $benar;
            $totalUji   += count($indeksUji);
            $detail[]    = array(
                'lipatan' => $ke + 1,
                'uji'     => count($indeksUji),
                'benar'   => $benar,
                'akurasi' => round(100 * $benar / count($indeksUji), 2),
            );
        }

        return array(
            'k'               => $k,
            'akurasi_rata2'   => $totalUji > 0 ? round(100 * $totalBenar / $totalUji, 2) : 0.0,
            'confusion_matrix'=> $gabung,
            'metrik_per_kelas'=> $this->metrikPerKelas($gabung),
            'lipatan'         => $detail,
        );
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    /** Presisi / Recall / F1 per kelas dari confusion matrix (aktual x prediksi). */
    public function metrikPerKelas(array $matriks): array
    {
        $out = array();
        foreach ($matriks as $aktual => $baris) {
            $tp = $baris[$aktual];
            $fp = 0;
            $fn = 0;
            foreach ($matriks as $x => $barisX) {
                if ($x !== $aktual) {
                    $fp += $barisX[$aktual]; // diprediksi $aktual padahal bukan
                    $fn += $baris[$x];       // aktual $aktual padahal diprediksi lain
                }
            }
            $presisi = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0.0;
            $recall  = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0.0;
            $f1      = ($presisi + $recall) > 0 ? 2 * $presisi * $recall / ($presisi + $recall) : 0.0;
            $out[$aktual] = array(
                'presisi'  => round($presisi, 4),
                'recall'   => round($recall, 4),
                'f1'       => round($f1, 4),
                'support'  => $tp + $fn,
            );
        }
        return $out;
    }

    /** Fisher-Yates dengan mt_rand (deterministik setelah mt_srand). */
    private function acak(array $arr): array
    {
        $n = count($arr);
        for ($i = $n - 1; $i > 0; $i--) {
            $j        = mt_rand(0, $i);
            $tmp      = $arr[$i];
            $arr[$i]  = $arr[$j];
            $arr[$j]  = $tmp;
        }
        return $arr;
    }

    private function kelasGabungan(array $rows, array $model): array
    {
        $kelas = array_unique(array_merge(
            $this->kolomUnikTerurut($rows),
            isset($model['kelas']) ? $model['kelas'] : array()
        ));
        sort($kelas);
        return array_values($kelas);
    }

    private function kolomUnikTerurut(array $rows): array
    {
        $kelas = array_unique($this->kolom($rows));
        sort($kelas);
        return array_values($kelas);
    }

    private function kolom(array $rows): array
    {
        $out = array();
        foreach ($rows as $r) {
            $out[] = $r[$this->target];
        }
        return $out;
    }
}
