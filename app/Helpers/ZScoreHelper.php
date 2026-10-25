<?php

namespace App\Helpers;

/**
 * Kalkulator Z-Score & Kategori Status Gizi
 * Standar: Permenkes No. 2 Tahun 2020
 *
 * Menggunakan interpolasi piecewise linear terhadap 7 titik SD
 * [m3, m2, m1, med, p1, p2, p3] untuk setiap baris dataset.
 */
class ZScoreHelper
{
    // ============================================================
    // INTERPOLASI PIECEWISE LINEAR
    // ============================================================
    /**
     * @param float $value  Nilai terukur (BB, TB, atau IMT)
     * @param array $sd7    [m3, m2, m1, med, p1, p2, p3]
     * @return float        Z-Score (bisa <-3 atau >+3 hasil ekstrapolasi)
     */
    public static function hitungZScore(float $value, array $sd7): float
    {
        [$m3, $m2, $m1, $med, $p1, $p2, $p3] = $sd7;

        if ($value >= $p3) {
            $den = $p3 - $p2;
            return $den > 0 ? 3 + (($value - $p3) / $den) : 3;
        }
        if ($value >= $p2) {
            $den = $p3 - $p2;
            return $den > 0 ? 2 + (($value - $p2) / $den) : 2;
        }
        if ($value >= $p1) {
            $den = $p2 - $p1;
            return $den > 0 ? 1 + (($value - $p1) / $den) : 1;
        }
        if ($value >= $med) {
            $den = $p1 - $med;
            return $den > 0 ? ($value - $med) / $den : 0;
        }
        if ($value >= $m1) {
            $den = $med - $m1;
            return $den > 0 ? ($value - $med) / $den : 0;
        }
        if ($value >= $m2) {
            $den = $m1 - $m2;
            return $den > 0 ? -1 + (($value - $m1) / $den) : -1;
        }
        if ($value >= $m3) {
            $den = $m2 - $m3;
            return $den > 0 ? -2 + (($value - $m2) / $den) : -2;
        }
        $den = $m2 - $m3;
        return $den > 0 ? -3 + (($value - $m3) / $den) : -3;
    }

    // ============================================================
    // KATEGORI STATUS GIZI (SESUAI PERMENKES 2020)
    // ============================================================
    public static function kategoriBBU(float $z): string
    {
        if ($z < -3) return 'Berat Badan Sangat Kurang';
        if ($z < -2) return 'Berat Badan Kurang';
        if ($z <= 1) return 'Berat Badan Normal';
        return 'Risiko Berat Badan Lebih';
    }

    public static function kategoriTBU(float $z): string
    {
        if ($z < -3) return 'Sangat Pendek';
        if ($z < -2) return 'Pendek';
        if ($z <= 3) return 'Normal';
        return 'Tinggi';
    }

    public static function kategoriBBTB(float $z): string
    {
        if ($z < -3) return 'Gizi Buruk';
        if ($z < -2) return 'Gizi Kurang';
        if ($z <= 1) return 'Gizi Baik';
        if ($z <= 2) return 'Berisiko Gizi Lebih';
        if ($z <= 3) return 'Gizi Lebih';
        return 'Obesitas';
    }

    public static function kategoriIMTU(float $z, int $umurBulan = 0): string
    {
        // 0-60 bulan: setara BB/TB
        if ($umurBulan <= 60) return self::kategoriBBTB($z);
        // 5-18 tahun (severely thinness, thinness, normal, overweight, obese)
        if ($z < -3) return 'Gizi Buruk';
        if ($z < -2) return 'Gizi Kurang';
        if ($z <= 1) return 'Gizi Baik';
        if ($z <= 2) return 'Gizi Lebih';
        return 'Obesitas';
    }

    // ============================================================
    // FACADE: STATUS BALITA (dipakai di controller)
    // ============================================================
    public static function statusBBU($gender, $umurBulan, $bb): string
    {
        $data = ZScoreDataset::getBBU($gender, (int) $umurBulan);
        if (!$data) return 'Tidak Diketahui';
        $z = self::hitungZScore((float) $bb, $data);
        return self::kategoriBBU($z);
    }

    public static function statusTBU($gender, $umurBulan, $tb): string
    {
        $data = ZScoreDataset::getTBU($gender, (int) $umurBulan);
        if (!$data) return 'Tidak Diketahui';
        $z = self::hitungZScore((float) $tb, $data);
        return self::kategoriTBU($z);
    }

    public static function statusBBTB($gender, $tb, $bb, int $umurBulan = 24): string
    {
        $data = ZScoreDataset::getBBTB($gender, (float) $tb, $umurBulan);
        if (!$data) return 'Tidak Diketahui';
        $z = self::hitungZScore((float) $bb, $data);
        return self::kategoriBBTB($z);
    }

    // ============================================================
    // STATUS IBU HAMIL
    // ============================================================
    public static function statusKEK($lila): string
    {
        if (!$lila) return 'Tidak Diketahui';
        return $lila < 23.5 ? 'Risiko KEK' : 'Tidak Risiko';
    }

    public static function statusAnemia($hb): string
    {
        if (!$hb) return 'Tidak Diketahui';
        return $hb < 11.0 ? 'Anemia' : 'Tidak Anemia';
    }

    public static function statusTD($td): string
    {
        if (!$td) return 'Tidak Diketahui';
        $parts = explode('/', $td);
        if (count($parts) !== 2) return 'Tidak Diketahui';
        $sis  = (int) $parts[0];
        $dias = (int) $parts[1];
        if ($sis >= 140 || $dias >= 90) return 'Hipertensi';
        if ($sis < 90  || $dias < 60)  return 'Hipotensi';
        return 'Normal';
    }

    // ============================================================
    // WARNA BADGE (untuk view)
    // ============================================================
    public static function getColorForStatus(?string $status): string
    {
        if (!$status) return '#f3f4f6';
        $s = strtolower($status);

        if (str_contains($s, 'normal') || str_contains($s, 'baik')
            || str_contains($s, 'tidak risiko') || str_contains($s, 'tidak anemia')) {
            return '#d1fae5';
        }
        if (str_contains($s, 'kurang') || str_contains($s, 'pendek')
            || str_contains($s, 'risiko')) {
            return '#fef3c7';
        }
        if (str_contains($s, 'buruk') || str_contains($s, 'sangat')
            || str_contains($s, 'anemia') || str_contains($s, 'hipertensi')) {
            return '#fee2e2';
        }
        if (str_contains($s, 'lebih') || str_contains($s, 'obesitas')
            || str_contains($s, 'gemuk') || str_contains($s, 'tinggi')) {
            return '#dbeafe';
        }
        return '#f3f4f6';
    }
}