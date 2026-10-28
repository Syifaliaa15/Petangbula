<?php

namespace App\Services\C45;

/**
 * Implementasi Algoritma C4.5 (Quinlan, 1993) untuk atribut KATEGORIK.
 *
 * PENTING (arsitektur): class ini dipanggil HANYA saat model dilatih
 * (tombol "Latih Model" oleh Admin/Bidan, atau `php artisan c45:train`),
 * BUKAN saat prediksi. Hasil training disimpan sebagai pohon JSON
 * (tabel c45_models) dan prediksi cukup menelusuri pohon -> milidetik.
 *
 * Rumus inti C4.5:
 *   Entropy(S)   = -SUM p_i * log2(p_i)
 *   Info(S, A)   = SUM (|Sv|/|S|) * Entropy(Sv)
 *   SplitInfo(A) = -SUM (|Sv|/|S|) * log2(|Sv|/|S|)
 *   GainRatio(A) = (Entropy(S) - Info(S, A)) / SplitInfo(A)
 *
 * Atribut pada sistem ini sudah KATEGORIK sejak awal (hasil diskretisasi
 * modul Z-Score): Tren_BB, Kelompok_Usia, Status_Gizi, LILA / Trimester,
 * LILA, Hb, Tensi. Kolom numerik (ZScore, LILA_cm, Hb_gdl, TD) dipakai
 * di hulu untuk gating & diskretisasi, tidak dipakai di dalam C4.5.
 */
class C45Trainer
{
    /** Jumlah sampel minimum agar sebuah node boleh dipecah (pre-pruning). */
    private int $minSamplesSplit;

    /** Kedalaman maksimum pohon (pre-pruning). */
    private int $maxDepth;

    public function __construct(int $minSamplesSplit = 2, int $maxDepth = 8)
    {
        $this->minSamplesSplit = $minSamplesSplit;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Entropy himpunan sampel S terhadap atribut target.
     * Contoh: 50% kelas A dan 50% kelas B -> 1.0 (ketidakpastian penuh);
     * semua sampel kelas sama -> 0.0 (murni).
     */
    public function entropy(array $rows, string $target): float
    {
        $n = count($rows);
        if ($n === 0) {
            return 0.0;
        }
        $jumlah = array_count_values($this->kolom($rows, $target));
        $h = 0.0;
        foreach ($jumlah as $c) {
            $p = $c / $n;
            $h -= $p * log($p, 2);
        }
        return $h;
    }

    /**
     * SplitInfo: ukuran "broadness" pemecahan atribut (pembagi Gain Ratio).
     * Atribut yang memecah jadi banyak cabang kecil punya SplitInfo besar.
     */
    public function splitInfo(array $partisi, int $total): float
    {
        $si = 0.0;
        foreach ($partisi as $subset) {
            $p = count($subset) / $total;
            if ($p > 0) {
                $si -= $p * log($p, 2);
            }
        }
        return $si;
    }

    /**
     * Pilih atribut terbaik berdasarkan Gain Ratio.
     *
     * @return array{atribut:?string, gain:float, gain_ratio:float, split_info:float,
     *               info:float, entropy:float, partisi:array}
     */
    public function atributTerbaik(array $rows, array $atribut, string $target): array
    {
        $entropyDasar = $this->entropy($rows, $target);
        $total = count($rows);

        $terbaik = array(
            'atribut'    => null,
            'gain'       => -1.0,
            'gain_ratio' => -1.0,
            'split_info' => 0.0,
            'info'       => 0.0,
            'entropy'    => $entropyDasar,
            'partisi'    => array(),
        );

        foreach ($atribut as $a) {
            $partisi = $this->partisi($rows, $a);
            if (count($partisi) < 2) {
                continue; // semua nilai sama -> tidak ada informasi
            }

            $info = 0.0;
            foreach ($partisi as $subset) {
                $info += (count($subset) / $total) * $this->entropy($subset, $target);
            }
            $splitInfo = $this->splitInfo($partisi, $total);
            $gain      = $entropyDasar - $info;
            $gainRatio = ($splitInfo > 0.0) ? ($gain / $splitInfo) : 0.0;

            // Kriteria deterministik: GainRatio tertinggi; seri -> Gain tertinggi;
            // seri lagi -> urutan daftar atribut (yang pertama menang).
            if ($gainRatio > $terbaik['gain_ratio'] + 1e-12
                || (abs($gainRatio - $terbaik['gain_ratio']) <= 1e-12
                    && $gain > $terbaik['gain'] + 1e-12)
            ) {
                $terbaik = array(
                    'atribut'    => $a,
                    'gain'       => $gain,
                    'gain_ratio' => $gainRatio,
                    'split_info' => $splitInfo,
                    'info'       => $info,
                    'entropy'    => $entropyDasar,
                    'partisi'    => $partisi,
                );
            }
        }

        return $terbaik;
    }

    /** Bangun pohon rekursif (fase utama C4.5). */
    public function bangunPohon(array $rows, array $atribut, string $target, int $kedalaman = 0): array
    {
        $n          = count($rows);
        $distribusi = array_count_values($this->kolom($rows, $target));
        ksort($distribusi);
        $mayoritas = $this->kelasMayoritas($distribusi);

        // Kondisi berhenti 1: node murni (semua kelas sama) -> daun
        if (count($distribusi) === 1) {
            return $this->daun((string) array_key_first($distribusi), $distribusi, $n);
        }
        // Kondisi berhenti 2: atribut habis / sampel terlalu sedikit / kedalaman maks
        if (empty($atribut) || $n < $this->minSamplesSplit || $kedalaman >= $this->maxDepth) {
            return $this->daun($mayoritas, $distribusi, $n);
        }

        $terbaik = $this->atributTerbaik($rows, $atribut, $target);

        // Kondisi berhenti 3: tidak ada atribut yang memberi Gain -> daun mayoritas
        if ($terbaik['atribut'] === null || $terbaik['gain'] <= 1e-12) {
            return $this->daun($mayoritas, $distribusi, $n);
        }

        $sisaAtribut = array_values(array_diff($atribut, array($terbaik['atribut'])));
        $cabang = array();
        foreach ($terbaik['partisi'] as $nilai => $subset) {
            $cabang[$nilai] = $this->bangunPohon($subset, $sisaAtribut, $target, $kedalaman + 1);
        }

        return array(
            'tipe'            => 'cabang',
            'atribut'         => $terbaik['atribut'],
            'entropy'         => round($terbaik['entropy'], 4),
            'info'            => round($terbaik['info'], 4),
            'split_info'      => round($terbaik['split_info'], 4),
            'gain'            => round($terbaik['gain'], 4),
            'gain_ratio'      => round($terbaik['gain_ratio'], 4),
            'jumlah_data'     => $n,
            'kelas_mayoritas' => $mayoritas, // fallback untuk nilai fitur tak dikenal
            'cabang'          => $cabang,
        );
    }

    /** Latih: bangun pohon + metadata model (struktur inilah yang disimpan JSON). */
    public function latih(array $rows, array $atribut, string $target): array
    {
        return array(
            'target'            => $target,
            'atribut'           => array_values($atribut),
            'kelas'             => array_values(array_unique($this->kolom($rows, $target))),
            'jumlah_data_latih' => count($rows),
            'dibangun_pada'     => date('c'),
            'parameter'         => array(
                'min_samples_split' => $this->minSamplesSplit,
                'max_depth'         => $this->maxDepth,
            ),
            'pohon'             => $this->bangunPohon($rows, $atribut, $target),
        );
    }

    // ------------------------------------------------------------------
    // Helper privat
    // ------------------------------------------------------------------

    /** Partisi baris berdasarkan nilai atribut kategorik (urutan nilai terurut). */
    private function partisi(array $rows, string $atribut): array
    {
        $out = array();
        foreach ($rows as $r) {
            $out[$r[$atribut]][] = $r;
        }
        ksort($out);
        return $out;
    }

    /** Kelas mayoritas; seri -> urut abjad (deterministik). */
    private function kelasMayoritas(array $distribusi): string
    {
        $maks = max($distribusi);
        $kandidat = array();
        foreach ($distribusi as $kelas => $c) {
            if ($c === $maks) {
                $kandidat[] = $kelas;
            }
        }
        sort($kandidat);
        return $kandidat[0];
    }

    private function daun(string $kelas, array $distribusi, int $n): array
    {
        return array(
            'tipe'        => 'daun',
            'kelas'       => $kelas,
            'jumlah_data' => $n,
            'distribusi'  => $distribusi,
        );
    }

    private function kolom(array $rows, string $kolom): array
    {
        $out = array();
        foreach ($rows as $r) {
            $out[] = $r[$kolom];
        }
        return $out;
    }
}
